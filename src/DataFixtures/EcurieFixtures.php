<?php

namespace App\DataFixtures;

use App\Entity\Ecurie;
use App\Entity\Moteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EcurieFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            MoteurFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $teams = [
            ['name' => 'Ferrari',   'moteur' => 'Ferrari'],
            ['name' => 'Red Bull',  'moteur' => 'Renault'],
            ['name' => 'McLarren',  'moteur' => 'Mercedes'],
        ];

        foreach ($teams as $t) {
            $moteur = $manager->getRepository(Moteur::class)->findOneBy(['name' => $t['moteur']]);

            if (!$moteur) {
                $moteur = new Moteur();
                $moteur->setName($t['moteur']);
                $manager->persist($moteur);
                $manager->flush(); 
            }

            $ecurie = new Ecurie();
            $ecurie->setName($t['name']);
            $ecurie->setMoteur($moteur);

            $manager->persist($ecurie);
        }

        $manager->flush();
    }
}