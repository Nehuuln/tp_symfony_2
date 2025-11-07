<?php

namespace App\DataFixtures;

use App\Entity\Pilote;
use App\Entity\Ecurie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PiloteFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            EcurieFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $data = [
            'Ferrari' => [
                ['nom' => 'Leclerc', 'prenom' => 'Charles'],
                ['nom' => 'Sainz',   'prenom' => 'Carlos'],
                ['nom' => 'Albon',   'prenom' => 'Alex'],
            ],
            'Red Bull' => [
                ['nom' => 'Verstappen', 'prenom' => 'Max'],
                ['nom' => 'Pérez',      'prenom' => 'Sergio'],
                ['nom' => 'Gasly',      'prenom' => 'Pierre'],
            ],
            'McLarren' => [
                ['nom' => 'Norris',   'prenom' => 'Lando'],
                ['nom' => 'Piastri',  'prenom' => 'Oscar'],
                ['nom' => 'DeVries',  'prenom' => 'Nyck'],
            ],
        ];

        foreach ($data as $ecurieName => $pilotes) {
            $ecurie = $manager->getRepository(Ecurie::class)->findOneBy(['name' => $ecurieName]);

            if (!$ecurie) {
                $ecurie = new Ecurie();
                $ecurie->setName($ecurieName);
                $manager->persist($ecurie);
                $manager->flush();
            }

            foreach ($pilotes as $i => $p) {
                $pilote = new Pilote();
                $pilote->setNom($p['nom']);
                $pilote->setPrenom($p['prenom']);
                $pilote->setLicencePoints(12);
                
                $pilote->setIsTitulaire($i < 2);

                $min = strtotime('2004-01-01');
                $max = strtotime('2023-12-31');
                $betweenDate = mt_rand($min, $max);
                $dt = new \DateTime();
                $dt->setTimestamp($betweenDate);
                $pilote->setStartedAt($dt);

                $pilote->setEcurie($ecurie);

                $manager->persist($pilote);
            }
        }

        $manager->flush();
    }
}