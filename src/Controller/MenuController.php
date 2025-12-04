<?php

namespace App\Controller;

use App\Repository\PizzaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/menu', name: 'app_menu_')]
class MenuController extends AbstractController
{
    public function __construct(
        private PizzaRepository $pizzaRepository,
    ) {}

    #[Route('/pizzas', name: 'list_pizzas', methods: ['GET'])]
    public function listPizzas(): JsonResponse
    {
        try {
            $pizzas = $this->pizzaRepository->findAll();

            $pizzasData = array_map(function ($pizza) {
                return [
                    'id' => $pizza->getId(),
                    'name' => $pizza->getName(),
                    'description' => $pizza->getDescription(),
                    'ingredients' => explode(',', $pizza->getIngredients()),
                    'sizes' => $pizza->getSizes(),
                    'image_url' => $pizza->getImageUrl(),
                    'category' => $pizza->getCategory(),
                    'is_available' => $pizza->isAvailable(),
                ];
            }, $pizzas);

            return $this->json([
                'success' => true,
                'message' => 'Pizzas obtenidas correctamente',
                'data' => $pizzasData,
            ], 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Error interno del servidor',
                'code' => 500,
            ], 500);
        }
    }

    #[Route('/categories', name: 'list_categories', methods: ['GET'])]
    public function listCategories(): JsonResponse
    {
        try {
            // Obtener todas las pizzas y extraer categorías únicas
            $pizzas = $this->pizzaRepository->findAll();

            $categories = [];
            foreach ($pizzas as $pizza) {
                $category = $pizza->getCategory();
                if ($category && !in_array($category, $categories)) {
                    $categories[] = $category;
                }
            }

            sort($categories);

            return $this->json([
                'success' => true,
                'message' => 'Categorías obtenidas correctamente',
                'data' => $categories,
            ], 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Error interno del servidor',
                'code' => 500,
            ], 500);
        }
    }

    #[Route('/prueba', name: 'test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        try {
            return $this->json([
                'success' => true,
                'message' => 'Funciona perfectamente',
            ], 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Error interno del servidor',
                'code' => 500,
            ], 500);
        }
    }
}
