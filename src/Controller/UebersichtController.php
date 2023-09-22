<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\WorkEntry;
use App\Repository\WorkEntryRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UebersichtController extends AbstractController
{
    #[Route('/uebersicht', name: 'app_uebersicht')]
    public function index(#[CurrentUser] User $user, WorkEntryRepository $workEntryRepository): Response
    {
        if(!$user)
        {
            return $this->redirect($this->generateUrl('app_login'));
        }

        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        return $this->render('uebersicht/index.html.twig', [
            'workEntry' => $workEntry,
        ]);
    }
}
