<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\PizzaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/orders', name: 'app_order_')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
        private PizzaRepository $pizzaRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validar entrada
            if (!isset($data['user_id'])) {
                return $this->json([
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Datos de entrada inválidos',
                    'details' => ['user_id' => 'El user_id es requerido'],
                    'code' => 400,
                ], 400);
            }

            if (!isset($data['items']) || !is_array($data['items']) || empty($data['items'])) {
                return $this->json([
                    'error' => 'EMPTY_ORDER',
                    'message' => 'El pedido no puede estar vacío',
                    'code' => 400,
                ], 400);
            }

            // Buscar usuario
            $user = $this->userRepository->find($data['user_id']);
            if (!$user) {
                return $this->json([
                    'error' => 'USER_NOT_FOUND',
                    'message' => 'Usuario no encontrado',
                    'code' => 404,
                ], 404);
            }

            // Crear orden
            $order = new Order();
            $order->setUser($user);
            $order->setStatus('pending');
            $order->setCreatedAt(new \DateTime());
            $order->setUpdatedAt(new \DateTime());

            $subtotal = 0;
            $taxRate = 0.21;

            // Procesar items
            foreach ($data['items'] as $item) {
                if (!isset($item['pizza_id']) || !isset($item['size']) || !isset($item['quantity'])) {
                    return $this->json([
                        'error' => 'VALIDATION_ERROR',
                        'message' => 'Datos de entrada inválidos',
                        'details' => ['items' => 'Cada item debe tener pizza_id, size y quantity'],
                        'code' => 400,
                    ], 400);
                }

                // Validar pizza
                $pizza = $this->pizzaRepository->find($item['pizza_id']);
                if (!$pizza) {
                    return $this->json([
                        'error' => 'PIZZA_NOT_FOUND',
                        'message' => 'Pizza no encontrada',
                        'code' => 404,
                    ], 404);
                }

                if (!$pizza->isAvailable()) {
                    return $this->json([
                        'error' => 'PIZZA_NOT_AVAILABLE',
                        'message' => 'La pizza seleccionada no está disponible',
                        'code' => 404,
                    ], 404);
                }

                // Obtener precio del tamaño
                $sizes = $pizza->getSizes();
                $sizePrice = null;
                foreach ($sizes as $size) {
                    if ($size['size'] === $item['size']) {
                        $sizePrice = (float) $size['price'];
                        break;
                    }
                }

                if ($sizePrice === null) {
                    return $this->json([
                        'error' => 'INVALID_SIZE',
                        'message' => 'Tamaño de pizza inválido',
                        'code' => 400,
                    ], 400);
                }

                // Crear item de orden
                $orderItem = new OrderItem();
                $orderItem->setOrderRelation($order);
                $orderItem->setPizzaId($pizza->getId());
                $orderItem->setPizzaName($pizza->getName());
                $orderItem->setSize($item['size']);
                $orderItem->setQuantity($item['quantity']);
                $orderItem->setUnitPrice((string) $sizePrice);

                $itemSubtotal = $sizePrice * $item['quantity'];
                $itemTaxAmount = $itemSubtotal * $taxRate;
                $itemTotal = $itemSubtotal + $itemTaxAmount;

                $orderItem->setItemSubtotal(number_format($itemSubtotal, 2, '.', ''));
                $orderItem->setTaxRate(number_format($taxRate, 2, '.', ''));
                $orderItem->setTaxAmount(number_format($itemTaxAmount, 2, '.', ''));
                $orderItem->setItemTotal(number_format($itemTotal, 2, '.', ''));

                $order->addOrderItem($orderItem);
                $subtotal += $itemSubtotal;
            }

            // Calcular totales
            $taxAmount = $subtotal * $taxRate;
            $totalAmount = $subtotal + $taxAmount;

            $order->setSubtotalAmount(number_format($subtotal, 2, '.', ''));
            $order->setTaxAmount(number_format($taxAmount, 2, '.', ''));
            $order->setTotalAmount(number_format($totalAmount, 2, '.', ''));

            // Guardar
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'data' => $this->formatOrder($order),
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Error interno del servidor',
                'debug' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    #[Route('/current', name: 'current', methods: ['GET'])]
    public function current(Request $request): JsonResponse
    {
        try {
            $userId = $request->query->get('user_id');

            if (!$userId) {
                return $this->json([
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Datos de entrada inválidos',
                    'details' => ['user_id' => 'El user_id es requerido'],
                    'code' => 400,
                ], 400);
            }

            $user = $this->userRepository->find($userId);
            if (!$user) {
                return $this->json([
                    'error' => 'USER_NOT_FOUND',
                    'message' => 'Usuario no encontrado',
                    'code' => 404,
                ], 404);
            }

            // Obtener última orden pendiente del usuario
            $order = $this->orderRepository->findOneBy([
                'user' => $user,
                'status' => 'pending'
            ], ['created_at' => 'DESC']);

            if (!$order) {
                return $this->json([
                    'success' => true,
                    'message' => 'No hay pedido pendiente',
                    'data' => null,
                ], 200);
            }

            return $this->json([
                'success' => true,
                'message' => 'Pedido obtenido correctamente',
                'data' => $this->formatOrder($order),
            ], 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Error interno del servidor',
                'code' => 500,
            ], 500);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $order = $this->orderRepository->find($id);

            if (!$order) {
                return $this->json([
                    'error' => 'ORDER_NOT_FOUND',
                    'message' => 'Pedido no encontrado',
                    'code' => 404,
                ], 404);
            }

            $data = json_decode($request->getContent(), true);

            // Validar datos
            if (isset($data['status'])) {
                $validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
                if (!in_array($data['status'], $validStatuses)) {
                    return $this->json([
                        'error' => 'VALIDATION_ERROR',
                        'message' => 'Datos de entrada inválidos',
                        'details' => ['status' => 'Estado de pedido inválido'],
                        'code' => 400,
                    ], 400);
                }
                $order->setStatus($data['status']);
            }

            if (isset($data['estimated_delivery_time'])) {
                try {
                    $order->setEstimatedDeliveryTime(new \DateTime($data['estimated_delivery_time']));
                } catch (\Exception $e) {
                    return $this->json([
                        'error' => 'VALIDATION_ERROR',
                        'message' => 'Datos de entrada inválidos',
                        'details' => ['estimated_delivery_time' => 'Fecha inválida'],
                        'code' => 400,
                    ], 400);
                }
            }

            $order->setUpdatedAt(new \DateTime());
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Pedido actualizado exitosamente',
                'data' => $this->formatOrder($order),
            ], 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Error interno del servidor',
                'code' => 500,
            ], 500);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $order = $this->orderRepository->find($id);

            if (!$order) {
                return $this->json([
                    'error' => 'ORDER_NOT_FOUND',
                    'message' => 'Pedido no encontrado',
                    'code' => 404,
                ], 404);
            }

            // Solo permitir eliminar órdenes pendientes
            if ($order->getStatus() !== 'pending') {
                return $this->json([
                    'error' => 'INVALID_ORDER_STATUS',
                    'message' => 'Solo se pueden eliminar pedidos pendientes',
                    'code' => 400,
                ], 400);
            }

            $this->entityManager->remove($order);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Pedido eliminado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Error interno del servidor',
                'code' => 500,
            ], 500);
        }
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id' => $order->getId(),
            'user_id' => $order->getUser()->getId(),
            'status' => $order->getStatus(),
            'subtotal_amount' => $order->getSubtotalAmount(),
            'tax_amount' => $order->getTaxAmount(),
            'total_amount' => $order->getTotalAmount(),
            'items' => array_map(function ($item) {
                return [
                    'id' => $item->getId(),
                    'pizza_id' => $item->getPizzaId(),
                    'pizza_name' => $item->getPizzaName(),
                    'size' => $item->getSize(),
                    'quantity' => $item->getQuantity(),
                    'unit_price' => $item->getUnitPrice(),
                    'item_subtotal' => $item->getItemSubtotal(),
                    'tax_rate' => $item->getTaxRate(),
                    'tax_amount' => $item->getTaxAmount(),
                    'item_total' => $item->getItemTotal(),
                ];
            }, $order->getOrderItems()->toArray()),
            'created_at' => $order->getCreatedAt()?->format('c'),
            'updated_at' => $order->getUpdatedAt()?->format('c'),
            'estimated_delivery_time' => $order->getEstimatedDeliveryTime()?->format('c'),
        ];
    }
}
