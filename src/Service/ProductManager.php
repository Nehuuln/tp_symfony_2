<?php 
// src/Service/MessageGenerator.php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Entity\Category;

class ProductManager
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function productManager(array $content): Product {
        $products = new Product();
        $products->setName($content['name']);
        $products->setUnitPrice((float)$content['unitPrice']);
        $products->setDescription($content['description']);
        $products->setCreatedAt(new \Datetime());
        $products->setStorage($content['storage']);

        $category = $this->entityManager->getRepository(Category::class)->find($content['categoryId']);
        $products->setCategory($category);

        $this->entityManager->persist($products);
        $this->entityManager->flush();

        return $products;
    }
}