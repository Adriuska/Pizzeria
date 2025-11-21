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
        // Verificar si el usuario ya existe
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        if ($existingUser) {
            $output->writeln('<info>El usuario de prueba ya existe.</info>');
            return Command::SUCCESS;
        }

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPhone('1234567890');
        $user->setAddress('123 Main St, Springfield');

        // Hashear la contraseña
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>Usuario de prueba creado exitosamente.</info>');
        $output->writeln('<info>Email: test@example.com</info>');
        $output->writeln('<info>Contraseña: password123</info>');

        return Command::SUCCESS;
    }
}
