<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Infraction;

class InfractionService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Applique les effets d'une infraction : décrémente les points du pilote
     * et met le statut à 'suspendu' si les points passent sous 12.
     */
    public function handleInfraction(Infraction $infraction): void
    {
        $pilote = $infraction->getPilote();
        if (!$pilote) {
            return;
        }

        $currentPoints = $pilote->getLicencePoints() ?? 0;
        $penalty = $infraction->getPointsPenalty() ?? 0;

        $newPoints = $currentPoints - $penalty;
        // Ne pas passer en dessous de 0
        if ($newPoints < 0) {
            $newPoints = 0;
        }

        $pilote->setLicencePoints((int)$newPoints);

        if ($newPoints < 12) {
            $pilote->setStatus('suspendu');
        }

        $this->em->persist($pilote);
        $this->em->flush();
    }
}
