<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\WorkEntry;
use App\Entity\Pause;
use App\Entity\PauseKategorie;
use App\Repository\WorkEntryKategorieRepository;
use App\Repository\WorkEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/stempelzeiten', name: 'stempelzeiten.')]
class StempelzeitenController extends AbstractController
{
    #[Route('/', name: 'anzeigen')]
    public function index(#[CurrentUser] User $user, WorkEntryRepository $workEntryRepository): Response
    {       
        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        $abgefragteTage = 60;

        $startDatum = \date("Y-m-d H:i:s", \strtotime("-{$abgefragteTage} Days"));
        $endDatum = \date("Y-m-d H:i:s", \time());

        $workEntries = $workEntryRepository->findByDateTimeRange($user->getId(), $startDatum, $endDatum);
        
        return $this->render('stempelzeiten/index.html.twig', [
            'workEntries' => $workEntries,
            'abgefragteTage' => $abgefragteTage,
            'workEntry' => $workEntry
        ]);
    }

    #[Route('/einstempeln', name: 'einstempeln')]
    public function einstempeln(
        #[CurrentUser] User $user,
        WorkEntryRepository $workEntryRepository,
        WorkEntryKategorieRepository $kategorieRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $workEntryCheck = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        if(!$workEntryCheck)
        {
            $workEntry = new WorkEntry();

            $workEntry->setStartDatum(new \DateTime());
            $workEntry->setUser($user);

            $kategorie = $kategorieRepository->findOneBy([
                'name' => 'Arbeit'
            ]);
            
            $workEntry->setKategorie($kategorie);

            $entityManager->persist($workEntry);
            $entityManager->flush();

        }
        return $this->redirect($this->generateUrl('app_uebersicht'));
    }

    

    #[Route('/ausstempeln', name: 'ausstempeln')]
    public function ausstempeln(
        #[CurrentUser] User $user,
        WorkEntryRepository $workEntryRepository,
        EntityManagerInterface $entityManager
    ): Response {


        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        if($workEntry)
        {            
            $workEntry->setEndeDatum(new \DateTime());

            $summeGesamt = $workEntry->getEndeDatum()->getTimestamp() - $workEntry->getStartDatum()->getTimestamp();

            foreach($workEntry->getPauses() as $key => $pause)
            {
                if($pause->getEndeDatum() == null)
                {
                    $pause->setEndeDatum(new \DateTime());
                }
            }
          
            $entityManager->flush();
        }   

        return $this->redirect($this->generateUrl('app_uebersicht'));
    }
}
