<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use App\Service\ProductManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

// ALTER TABLE product AUTO_INCREMENT=1

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    private ProductManager $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    public function load(ObjectManager $manager): void
    {
        $productNames = [
            'Casque bluetooth', 'Smartphone', 'Liseuse', 'Roman', 'Livre pour enfants',
            'T-shirt', 'Veste', 'Jeans', 'Cafetière', 'Blender',
            'Chaise', 'Lampe LED', 'Coussin', 'Figurine', 'Jeu de société',
            'Blocs de construction', 'Fitness Tracker', 'Chargeur sans fil', 'Housse d\'ordinateur portable', 'Organisateur de bureau'
        ];

        foreach ($productNames as $name) {
            $data = [
            'name' => $name,
            'unitPrice' => mt_rand(100, 900),
            'createdAt' => new \Datetime(),
            'description' => 'Description for ' . $name,
            'storage' => mt_rand(0, 50),
            'categoryId' => mt_rand(1, 5),
        ];

        $this->productManager->productManager($data);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}