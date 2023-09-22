<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\WorkEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class StatusController extends AbstractController
{
    #[Route('/status/ajax', name: 'status')]
    public function ajaxAction(#[CurrentUser] User $user, Request $request, WorkEntryRepository $workEntryRepository): Response
    {
        if($request->isXmlHttpRequest() || $request->query->get('showJson') == 1)
        {            
            $jsonData = array();

            $workEntry = $workEntryRepository->findOneBy([
                'user' => $user->getId(),
                'endeDatum' => null
            ]);

            $jsonData['workEntryAktiv'] = false;

            if($workEntry)
            {

                $jsonData['workEntryAktiv'] = true;
                $jsonData['workEntryStartDatum'] = date("d.m.Y",$workEntry->getStartDatum()->getTimestamp());
                $jsonData['workEntryStartZeit'] = date("H:i:s",$workEntry->getStartDatum()->getTimestamp());
                $jsonData['pauseAktiv'] = false;

                foreach($workEntry->getPauses() as $key => $pause)
                {
                    if($pause->getEndeDatum() == null)
                    {
                        $jsonData['pauseAktiv'] = true;     
                        $jsonData['pauseStartDatum'] = date("d.m.Y",$pause->getStartDatum()->getTimestamp());
                        $jsonData['pauseStartZeit'] = date("H:i:s",$pause->getStartDatum()->getTimestamp());
                        $jsonData['pauseKategorie'] = $pause->getKategorie()->getName();
                        break;
                    }
                }
            }
            return new JsonResponse($jsonData);
        }
        else
        {

            return $this->render('status/index.html.twig', [
                'controller_name' => 'StatusController',
            ]);
        }
    }
}
