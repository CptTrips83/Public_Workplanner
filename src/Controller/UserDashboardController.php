<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WorkEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserDashboardController extends AbstractController
{
    #[Route('/user/dashboard/ajax', name: 'user_dashboard_ajax')]
    public function ajaxAction(Request $request, WorkEntryRepository $workEntryRepository, UserRepository $userRepository): Response
    {
        if($request->isXmlHttpRequest() || $request->query->get('showJson') == 1)
        {  
            $users = $userRepository->findBy([
                'aktiv' => true,
                'showOnDashboard' => true
            ], [
                'nachname' => 'ASC'
            ]);

            $jsonData = array();

            foreach($users as $key => $user)
            {

                $temp = array();

                $temp['userId'] = $user->getId();
                $temp['vorname'] = $user->getVorname();
                $temp['nachname'] = $user->getNachname();
                $temp['workEntryAktiv'] = false;
                $temp['pauseAktiv'] = false;

                $workEntry = $workEntryRepository->findOneBy([
                    'user' => $user->getId(),
                    'endeDatum' => null
                ]);

                if($workEntry)
                {                
                    $temp['workEntryAktiv'] = true;
                    $temp['workEntryStartDatum'] = date("d.m.Y",$workEntry->getStartDatum()->getTimestamp());
                    $temp['workEntryStartZeit'] = date("H:i:s",$workEntry->getStartDatum()->getTimestamp());

                    foreach($workEntry->getPauses() as $key => $pause)
                    {
                        if($pause->getEndeDatum() == null)
                        {                        
                            $temp['pauseAktiv'] = true;
                            $temp['pauseStartDatum'] = date("d.m.Y",$pause->getStartDatum()->getTimestamp());
                            $temp['pauseStartZeit'] = date("H:i:s",$pause->getStartDatum()->getTimestamp());
                            $temp['pauseKategorie'] = $pause->getKategorie()->getName();

                            break;
                        }
                    }
                }

                $jsonData[] = $temp;
            }

            return new JsonResponse($jsonData);
        }
        else
        {
            return $this->render('user_dashboard/index.html.twig', [
                'controller_name' => 'UserDashboardController',
            ]);
        }
    }

    #[Route('/user/dashboard', name: 'user_dashboard')]
    public function index(#[CurrentUser] User $user, WorkEntryRepository $workEntryRepository)
    {
        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        return $this->render('user_dashboard/index.html.twig', [
            'workEntry' => $workEntry,
        ]);
    }
}
