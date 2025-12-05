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
                'image_url' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=900&q=80',
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
                'image_url' => 'https://cdn.pixabay.com/photo/2017/12/09/08/18/pizza-3007395_1280.jpg',
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
                'image_url' => 'https://images.unsplash.com/photo-1528137871618-79d2761e3fd5?auto=format&fit=crop&w=900&q=80',
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
                'image_url' => 'https://images.unsplash.com/photo-1506354666786-959d6d497f1a?auto=format&fit=crop&w=900&q=80',
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
                'image_url' => 'https://images.unsplash.com/photo-1541745537411-b8046dc6d66c?auto=format&fit=crop&w=900&q=80',
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
                'image_url' => 'https://images.pexels.com/photos/315755/pexels-photo-315755.jpeg?auto=compress&cs=tinysrgb&w=900',
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
