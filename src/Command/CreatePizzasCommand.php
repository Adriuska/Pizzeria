<?php

namespace App\Command;

use App\Entity\Pizza;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-pizzas',
    description: 'Crea pizzas de ejemplo en la base de datos',
)]
class CreatePizzasCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Verificar si ya existen pizzas
        $pizzasCount = $this->entityManager->getRepository(Pizza::class)->count([]);
        if ($pizzasCount > 0) {
            $output->writeln('<info>Ya existen pizzas en la base de datos. Saltando inserción.</info>');
            return Command::SUCCESS;
        }

        $pizzas = [
            [
                'name' => 'Margherita',
                'description' => 'Pizza clásica con tomate, queso mozarela y albahaca fresca',
                'ingredients' => 'Tomate, Mozarela, Albahaca, Aceite de oliva',
                'category' => 'Clásicas',
                'image_url' => 'https://via.placeholder.com/300?text=Margherita',
                'sizes' => [
                    ['size' => 'small', 'price' => '8.99', 'diameter' => '25cm', 'currency' => 'USD'],
                    ['size' => 'medium', 'price' => '12.99', 'diameter' => '30cm', 'currency' => 'USD'],
                    ['size' => 'large', 'price' => '16.99', 'diameter' => '35cm', 'currency' => 'USD'],
                ],
            ],
            [
                'name' => 'Pepperoni',
                'description' => 'Pizza con tomate, queso y rodajas de pepperoni',
                'ingredients' => 'Tomate, Mozarela, Pepperoni',
                'category' => 'Carnes',
                'image_url' => 'https://via.placeholder.com/300?text=Pepperoni',
                'sizes' => [
                    ['size' => 'small', 'price' => '9.99', 'diameter' => '25cm', 'currency' => 'USD'],
                    ['size' => 'medium', 'price' => '13.99', 'diameter' => '30cm', 'currency' => 'USD'],
                    ['size' => 'large', 'price' => '17.99', 'diameter' => '35cm', 'currency' => 'USD'],
                ],
            ],
            [
                'name' => 'Hawái',
                'description' => 'Pizza con jamón, queso y piña',
                'ingredients' => 'Tomate, Mozarela, Jamón, Piña',
                'category' => 'Tropical',
                'image_url' => 'https://via.placeholder.com/300?text=Hawaii',
                'sizes' => [
                    ['size' => 'small', 'price' => '10.99', 'diameter' => '25cm', 'currency' => 'USD'],
                    ['size' => 'medium', 'price' => '14.99', 'diameter' => '30cm', 'currency' => 'USD'],
                    ['size' => 'large', 'price' => '18.99', 'diameter' => '35cm', 'currency' => 'USD'],
                ],
            ],
            [
                'name' => 'Cuatro Quesos',
                'description' => 'Pizza con mozarela, cheddar, azul y parmesano',
                'ingredients' => 'Mozarela, Cheddar, Queso Azul, Parmesano',
                'category' => 'Vegetariana',
                'image_url' => 'https://via.placeholder.com/300?text=Cuatro+Quesos',
                'sizes' => [
                    ['size' => 'small', 'price' => '11.99', 'diameter' => '25cm', 'currency' => 'USD'],
                    ['size' => 'medium', 'price' => '15.99', 'diameter' => '30cm', 'currency' => 'USD'],
                    ['size' => 'large', 'price' => '19.99', 'diameter' => '35cm', 'currency' => 'USD'],
                ],
            ],
            [
                'name' => 'Carne de Res',
                'description' => 'Pizza con carne molida, cebolla y champiñones',
                'ingredients' => 'Tomate, Mozarela, Carne Molida, Cebolla, Champiñones',
                'category' => 'Carnes',
                'image_url' => 'https://via.placeholder.com/300?text=Carne+de+Res',
                'sizes' => [
                    ['size' => 'small', 'price' => '11.99', 'diameter' => '25cm', 'currency' => 'USD'],
                    ['size' => 'medium', 'price' => '15.99', 'diameter' => '30cm', 'currency' => 'USD'],
                    ['size' => 'large', 'price' => '19.99', 'diameter' => '35cm', 'currency' => 'USD'],
                ],
            ],
            [
                'name' => 'Vegetariana Supreme',
                'description' => 'Pizza con brócoli, tomate, cebolla, pimiento y champiñones',
                'ingredients' => 'Brócoli, Tomate, Cebolla, Pimiento Rojo, Champiñones, Aceitunas Negras',
                'category' => 'Vegetariana',
                'image_url' => 'https://via.placeholder.com/300?text=Vegetariana+Supreme',
                'sizes' => [
                    ['size' => 'small', 'price' => '10.99', 'diameter' => '25cm', 'currency' => 'USD'],
                    ['size' => 'medium', 'price' => '14.99', 'diameter' => '30cm', 'currency' => 'USD'],
                    ['size' => 'large', 'price' => '18.99', 'diameter' => '35cm', 'currency' => 'USD'],
                ],
            ],
        ];

        foreach ($pizzas as $pizzaData) {
            $pizza = new Pizza();
            $pizza->setName($pizzaData['name']);
            $pizza->setDescription($pizzaData['description']);
            $pizza->setIngredients($pizzaData['ingredients']);
            $pizza->setCategory($pizzaData['category']);
            $pizza->setImageUrl($pizzaData['image_url']);
            $pizza->setSizes($pizzaData['sizes']);
            $pizza->setIsAvailable(true);

            $this->entityManager->persist($pizza);
        }

        $this->entityManager->flush();

        $output->writeln('<info>Se han creado ' . count($pizzas) . ' pizzas exitosamente.</info>');

        return Command::SUCCESS;
    }
}
