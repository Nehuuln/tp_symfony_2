<?php
namespace App\DataFixtures;

use App\Entity\Moteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MoteurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brands = ['Ferrari', 'Renault', 'Mercedes'];

        foreach ($brands as $brand) {
            $moteur = new Moteur();
            $moteur->setName($brand);
            $manager->persist($moteur);
        }

        $manager->flush();
    }
}