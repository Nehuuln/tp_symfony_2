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

#[Route('/api/pilote')]
class PiloteController extends AbstractController
{
    #[Route('/ecurie/{id}/edit', name: 'edit_ecurie_pilotes', methods: ['PATCH', 'PUT'])]
    public function editPilotesOfEcurie(Request $request, EntityManagerInterface $em, int $id): JsonResponse
    {

        try {
            $content = json_decode($request->getContent(), true);

            if (!is_array($content) || empty($content)) {
                return new JsonResponse(['error' => 'Corps JSON invalide ou vide, attendu: tableau d\'objets pilotes'], JsonResponse::HTTP_BAD_REQUEST);
            }
            $ecurie = $em->getRepository(Ecurie::class)->find($id);
            if (!$ecurie) {
                return new JsonResponse(['error' => 'Écurie introuvable'], JsonResponse::HTTP_NOT_FOUND);
            }

            $pilotesRepo = $em->getRepository(Pilote::class);
            $updatedPilotes = [];

                if (!isset($content['id'])) {
                    return new JsonResponse(['error' => 'Pour modifier un pilote, le champ id est requis pour chaque objet'], JsonResponse::HTTP_BAD_REQUEST);
                }

                $pilote = $pilotesRepo->find((int) $content['id']);
                if (!$pilote) {
                    return new JsonResponse(['error' => "Pilote id {$content['id']} introuvable"], JsonResponse::HTTP_NOT_FOUND);
                }

                $piloteEcurie = $pilote->getEcurie();
                if (!$piloteEcurie || $piloteEcurie->getId() !== $ecurie->getId()) {
                    return new JsonResponse(['error' => "Le pilote id {$content['id']} n'appartient pas à cette écurie"], JsonResponse::HTTP_BAD_REQUEST);
                }

                if (isset($content['nom'])) {
                    $pilote->setNom($content['nom']);
                }
                if (isset($content['prenom'])) {
                    $pilote->setPrenom($content['prenom']);
                }
                if (isset($content['licencePoints'])) {
                    $pilote->setLicencePoints((int) $content['licencePoints']);
                }
                if (isset($content['isTitulaire'])) {
                    $pilote->setIsTitulaire((bool) $content['isTitulaire']);
                }
                if (isset($content['startedAt'])) {
                    try {
                        $pilote->setStartedAt(new \DateTime($content['startedAt']));
                    } catch (\Exception $e) {
                        return new JsonResponse(['error' => "Format de date invalide pour startedAt du pilote id {$content['id']}"], JsonResponse::HTTP_BAD_REQUEST);
                    }
                }

                $em->persist($pilote);

                $updatedPilotes[] = [
                    'id' => $pilote->getId(),
                    'nom' => $pilote->getNom(),
                    'prenom' => $pilote->getPrenom(),
                    'licencePoints' => $pilote->getLicencePoints(),
                    'isTitulaire' => $pilote->isTitulaire(),
                    'startedAt' => $pilote->getStartedAt() ? $pilote->getStartedAt()->format(\DateTime::ATOM) : null,
                ];

            $em->flush();

            return new JsonResponse([
                'message' => 'Pilotes modifiés',
                'pilotes' => $updatedPilotes,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur serveur: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}