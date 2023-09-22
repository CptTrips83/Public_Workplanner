<?php

namespace App\Controller;

use App\Entity\Pause;
use App\Entity\User;
use App\Entity\WorkEntry;
use App\Repository\PauseKategorieRepository;
use App\Repository\UserRepository;
use App\Repository\WorkEntryKategorieRepository;
use App\Repository\WorkEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SimulationController extends AbstractController
{
    #[Route('/simulation', name: 'app_simulation')]
    public function index(PauseKategorieRepository $pauseRepository, WorkEntryKategorieRepository $kategorieRepository, UserRepository $userRepository, WorkEntryRepository $workEntryRepository, EntityManagerInterface $entityManager): Response
    {
        $simUser = $userRepository->findBy([
            'username' => ['hknorz', 'tmitarbeiter', 'gwork']
        ]);

        foreach($simUser as $key => $user)
        {

            $workEntry = $workEntryRepository->findOneBy([
                'user' => $user->getId(),
                'endeDatum' => null
            ]);

            if($workEntry &&
                date('H') >= 23)
            {
                $isWorkDone = rand(0, 100);

                if($isWorkDone > 20)
                {
                    $workEntry->setEndeDatum(new \DateTime());
                    $pause = $workEntry->getAktivePause();
                    if($pause)
                    {                        
                        $pause->setEndeDatum(new \DateTime());
                        $entityManager->persist($pause);
                    }
                }
            }
            if(!$workEntry && 
                date('H') > 9 && 
                date('H') < 23)
            {
                $isWorkStart = rand(0, 100);

                if($isWorkStart > 40)
                {
                    $workEntry = new WorkEntry();

                    $workEntry->setStartDatum(new \DateTime());
                    $workEntry->setUser($user);

                    $kategorie = $kategorieRepository->findOneBy([
                        'name' => 'Arbeit'
                    ]);
                    
                    $workEntry->setKategorie($kategorie);            
                }
            }

            if($workEntry &&
                !$workEntry->getAktivePause())
            {
                $isPauseNeeded = rand(0, 100);

                if($isPauseNeeded < 20)
                {
                    $pause = new Pause();

                    $pauseKategories = $pauseRepository->findBy([
                        'aktiv' => '1'
                    ]);

                    $kategorieIndex = rand(0, count($pauseKategories) - 1);

                    $pause->setWorkentry($workEntry);
                    $pause->setKategorie($pauseKategories[$kategorieIndex]);
                    $pause->setStartDatum(new \DateTime());

                    $entityManager->persist($pause);
                }
            }

            if($workEntry &&
                $workEntry->getAktivePause())
            {
                $isPauseNeeded = rand(0, 100);

                if($isPauseNeeded < 30)
                {
                    $pause = $workEntry->getAktivePause();

                    $pause->setEndeDatum(new \DateTime());

                    $entityManager->persist($pause);
                }
            }

            $entityManager->persist($workEntry);
            $entityManager->flush();

        }
        return $this->render('simulation/index.html.twig', [
            'controller_name' => 'SimulationController',
        ]);
    }
   
}
