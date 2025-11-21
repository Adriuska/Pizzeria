<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth', name: 'app_auth_')]
class AuthController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validar entrada
            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->json([
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Datos de entrada inválidos',
                    'details' => [
                        'email' => !isset($data['email']) ? 'El email es requerido' : null,
                        'password' => !isset($data['password']) ? 'La contraseña es requerida' : null,
                    ],
                    'code' => 400,
                ], 400);
            }

            $email = trim($data['email']);
            $password = $data['password'];

            // Validar formato
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->json([
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Datos de entrada inválidos',
                    'details' => [
                        'email' => 'El email debe ser válido',
                    ],
                    'code' => 400,
                ], 400);
            }

            if (strlen($password) < 6) {
                return $this->json([
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Datos de entrada inválidos',
                    'details' => [
                        'password' => 'La contraseña debe tener al menos 6 caracteres',
                    ],
                    'code' => 400,
                ], 400);
            }

            // Buscar usuario
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
                return $this->json([
                    'error' => 'INVALID_CREDENTIALS',
                    'message' => 'Email o contraseña incorrectos',
                    'code' => 401,
                ], 401);
            }

            return $this->json([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'data' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getName(),
                    'token' => $user->getToken(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => 'Error interno del servidor',
                'code' => 500,
            ], 500);
        }
    }

    #[Route('/forgot-password', name: 'forgot_password', methods: ['POST'])]
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validar entrada
            if (!isset($data['email'])) {
                return $this->json([
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Datos de entrada inválidos',
                    'details' => [
                        'email' => 'El email es requerido',
                    ],
                    'code' => 400,
                ], 400);
            }

            $email = trim($data['email']);

            // Validar formato
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->json([
                    'error' => 'VALIDATION_ERROR',
                    'message' => 'Datos de entrada inválidos',
                    'details' => [
                        'email' => 'El email debe ser válido',
                    ],
                    'code' => 400,
                ], 400);
            }

            // Buscar usuario
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                // Por seguridad, no revelar si el email existe
                return $this->json([
                    'success' => true,
                    'message' => 'Si el email existe, recibirá un correo de recuperación',
                ], 200);
            }

            // Generar nuevo token de recuperación
            $user->setToken(bin2hex(random_bytes(32)));
            $this->entityManager->flush();

            // TODO: Enviar email con link de recuperación
            // $this->sendPasswordResetEmail($user);

            return $this->json([
                'success' => true,
                'message' => 'Si el email existe, recibirá un correo de recuperación',
                'data' => [
                    'reset_token' => $user->getToken(), // En producción, esto no se devolvería aquí
                ],
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
