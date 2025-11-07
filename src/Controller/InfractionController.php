<?php

namespace App\Controller;

use App\Entity\Ecurie;
use App\Entity\Pilote;
use App\Entity\Infraction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/infraction')]
class InfractionController extends AbstractController
{
#[Route('/create', name: 'create_infraction', methods: ['POST'])]
    public function createInfraction(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || empty($data)) {
            return new JsonResponse(['error' => 'Corps JSON invalide ou vide'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $hasPilote = isset($data['piloteId']);
        $hasEcurie = isset($data['ecurieId']);

        if (!($hasPilote || $hasEcurie)) {
            return new JsonResponse(['error' => 'Fournir soit piloteId soit ecurieId (exactement un)'], JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach (['raceName', 'description', 'pointsPenalty'] as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return new JsonResponse(['error' => "Le champ {$field} est requis"], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        $infraction = new Infraction();
        $infraction->setRaceName((string)$data['raceName']);
        $infraction->setDescription((string)$data['description']);
        $infraction->setPointsPenalty((int)$data['pointsPenalty']);

        if (isset($data['occuredAt'])) {
            try {
                $infraction->setOccuredAt(new \DateTime($data['occuredAt']));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Format de date occuredAt invalide'], JsonResponse::HTTP_BAD_REQUEST);
            }
        } else {
            $infraction->setOccuredAt(new \DateTime());
        }

        if ($hasPilote) {
            $pilote = $em->getRepository(Pilote::class)->find((int)$data['piloteId']);
            if (!$pilote) {
                return new JsonResponse(['error' => 'Pilote introuvable'], JsonResponse::HTTP_NOT_FOUND);
            }
            $infraction->setPilote($pilote);
        }

        if ($hasEcurie) {
            $ecurie = $em->getRepository(Ecurie::class)->find((int)$data['ecurieId']);
            if (!$ecurie) {
                return new JsonResponse(['error' => 'Écurie introuvable'], JsonResponse::HTTP_NOT_FOUND);
            }
            $infraction->setEcurie($ecurie);
        }

        $em->persist($infraction);
        $em->flush();

        $result = [
            'id' => $infraction->getId(),
            'raceName' => $infraction->getRaceName(),
            'occuredAt' => $infraction->getOccuredAt() ? $infraction->getOccuredAt()->format(\DateTime::ATOM) : null,
            'description' => $infraction->getDescription(),
            'pointsPenalty' => $infraction->getPointsPenalty(),
            'piloteId' => $infraction->getPilote() ? $infraction->getPilote()->getId() : null,
            'ecurieId' => $infraction->getEcurie() ? $infraction->getEcurie()->getId() : null,
        ];

        return new JsonResponse(['message' => 'Infraction créée', 'infraction' => $result], JsonResponse::HTTP_CREATED);
    }
}