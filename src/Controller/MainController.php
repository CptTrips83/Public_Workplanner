<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\WorkEntry;
use App\Repository\WorkEntryRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(#[CurrentUser] User $user, WorkEntryRepository $workEntryRepository): Response
    {
        
        

        return $this->redirect($this->generateUrl('app_uebersicht'));
    }
}
