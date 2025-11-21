<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-test-user',
    description: 'Crea un usuario de prueba',
)]
class CreateTestUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Definir usuarios de prueba
        $testUsers = [
            [
                'email' => 'test@example.com',
                'password' => 'password123',
                'name' => 'Test User',
                'phone' => '1234567890',
                'address' => '123 Main St, Springfield',
            ],
            [
                'email' => 'juan@example.com',
                'password' => 'password123',
                'name' => 'Juan López',
                'phone' => '9876543210',
                'address' => '456 Oak Ave, Springfield',
            ],
            [
                'email' => 'maria@example.com',
                'password' => 'password123',
                'name' => 'María García',
                'phone' => '5555555555',
                'address' => '789 Pine Rd, Springfield',
            ],
        ];

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($testUsers as $userData) {
            // Verificar si el usuario ya existe
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]);
            if ($existingUser) {
                $output->writeln("<comment>⊘ Usuario {$userData['email']} ya existe.</comment>");
                $skippedCount++;
                continue;
            }

            $user = new User();
            $user->setEmail($userData['email']);
            $user->setName($userData['name']);
            $user->setPhone($userData['phone']);
            $user->setAddress($userData['address']);

            // Hashear la contraseña
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $output->writeln("<info>✓ Usuario {$userData['email']} creado exitosamente.</info>");
            $output->writeln("  Email: {$userData['email']}");
            $output->writeln("  Contraseña: {$userData['password']}");
            $createdCount++;
        }

        $output->writeln('');
        $output->writeln("<info>Resumen: {$createdCount} usuarios creados, {$skippedCount} saltados.</info>");

        return Command::SUCCESS;
    }
}
