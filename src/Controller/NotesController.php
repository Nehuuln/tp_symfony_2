<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotesController extends AbstractController
{
    #[Route('/eleves/notes/{note1}/{note2}/{note3}/{note4}', name: 'random_notes', requirements: ['note1' => '\d+', 'note2' => '\d+', 'note3' => '\d+', 'note4' => '\d+'])]
    public function note(int $note1, int $note2, int $note3, int $note4): Response
{
    /* $note1 = random_int(0, 20);
    $note2 = random_int(0, 20);
    $note3 = random_int(0, 20);
    $note4 = random_int(0, 20); */

    return $this->render('notes/notes.html.twig', [
        'note1' => $note1,
        'note2' => $note2,
        'note3' => $note3,
        'note4' => $note4,
    ]);
  }
}