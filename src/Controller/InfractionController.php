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

    #[Route('/list', name: 'list_infractions', methods: ['GET'])]
    public function listInfractions(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $ecurieId = $request->query->get('ecurieId');
        $piloteId = $request->query->get('piloteId');
        $date = $request->query->get('date');
        $dateFrom = $request->query->get('dateFrom');
        $dateTo = $request->query->get('dateTo');

        try {
            $qb = $em->getRepository(Infraction::class)->createQueryBuilder('i');

            if ($ecurieId) {
                $qb->andWhere('i.ecurie = :ecurieId')->setParameter('ecurieId', (int)$ecurieId);
            }

            if ($piloteId) {
                $qb->andWhere('i.driver = :piloteId OR i.driver = :piloteId')->setParameter('piloteId', (int)$piloteId);
            }

            if ($date) {
                $d = new \DateTime($date);
                $start = (clone $d)->setTime(0, 0, 0);
                $end = (clone $d)->setTime(23, 59, 59);
                $qb->andWhere('i.occuredAt BETWEEN :start AND :end')
                   ->setParameter('start', $start)
                   ->setParameter('end', $end);
            } else {
                if ($dateFrom) {
                    $df = new \DateTime($dateFrom);
                    $qb->andWhere('i.occuredAt >= :from')->setParameter('from', $df->setTime(0,0,0));
                }
                if ($dateTo) {
                    $dt = new \DateTime($dateTo);
                    $qb->andWhere('i.occuredAt <= :to')->setParameter('to', $dt->setTime(23,59,59));
                }
            }

            $qb->orderBy('i.occuredAt', 'DESC');

            $infractions = $qb->getQuery()->getResult();

            if (empty($infractions) && ($ecurieId || $piloteId)) {
                if ($ecurieId && $piloteId) {
                    return new JsonResponse(['error' => "Aucune infraction trouvée pour l\'écurie id {$ecurieId} et le pilote id {$piloteId}"], JsonResponse::HTTP_NOT_FOUND);
                }
                if ($ecurieId) {
                    return new JsonResponse(['error' => "Aucune infraction trouvée pour l\'écurie id {$ecurieId}"], JsonResponse::HTTP_NOT_FOUND);
                }
                if ($piloteId) {
                    return new JsonResponse(['error' => "Aucune infraction trouvée pour le pilote id {$piloteId}"], JsonResponse::HTTP_NOT_FOUND);
                }
            }

            $result = [];
            foreach ($infractions as $infraction) {
                $result[] = [
                    'id' => $infraction->getId(),
                    'raceName' => $infraction->getRaceName(),
                    'occuredAt' => $infraction->getOccuredAt() ? $infraction->getOccuredAt()->format(\DateTime::ATOM) : null,
                    'description' => $infraction->getDescription(),
                    'pointsPenalty' => $infraction->getPointsPenalty(),
                    'piloteId' => $infraction->getPilote() ? $infraction->getPilote()->getId() : null,
                    'ecurieId' => $infraction->getEcurie() ? $infraction->getEcurie()->getId() : null,
                ];
            }

            return new JsonResponse(['infractions' => $result], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur serveur: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}