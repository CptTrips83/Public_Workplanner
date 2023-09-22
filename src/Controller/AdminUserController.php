<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WorkEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin', name: 'admin.')]
class AdminUserController extends AbstractController
{
    #[Route('/user/anzeigen', name: 'user_anzeigen')]
    public function index(#[CurrentUser] User $user, UserRepository $userRepository, WorkEntryRepository $workEntryRepository): Response
    {
        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        $users = $userRepository->findBy([
            'aktiv' => true
        ],[
            'nachname' => 'asc'
        ]);


        return $this->render('admin_user/index.html.twig', [
            'workEntry' => $workEntry,
            'users' => $users
        ]);
    }
}
