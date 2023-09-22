<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\WorkEntry;
use App\Entity\Pause;
use App\Entity\PauseKategorie;
use App\Repository\PauseKategorieRepository;
use App\Repository\PauseRepository;
use App\Repository\WorkEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/pause', name: 'pause.')]
class PauseController extends AbstractController
{
    #[Route('/', name: 'anzeigen')]
    public function anzeigen(#[CurrentUser] User $user, Request $request, WorkEntryRepository $workEntryRepository, EntityManagerInterface $entityManager): Response
    {
        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);        

        $AktivePause = null;

        foreach($workEntry->getPauses() as $key => $pause)
        {
            if($pause->getEndeDatum() == null)
            {
                $AktivePause = $pause;
                break;
            }
        }

        return $this->render('pause/index.html.twig', [
            'workEntry' => $workEntry,
            'AktivePause' => $AktivePause
        ]);
    }

    #[Route('/start', name: 'start')]
    public function pauseStart(#[CurrentUser] User $user, Request $request, WorkEntryRepository $workEntryRepository, EntityManagerInterface $entityManager): Response
    {
        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);
        

        $pauseform = $this->createFormBuilder()                        
            ->add('kategorie', EntityType::class, [
                'class' => PauseKategorie::class,
                'query_builder' => function (PauseKategorieRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('p')
                        ->where('p.aktiv = 1');
                }
            ])
            ->add('speichern', SubmitType::class)
            ->getForm();

        //Speichert Ergebnis aus Request ins Formular
        $pauseform->handleRequest($request);

        if($pauseform->isSubmitted()) {
            
            $pause = new Pause();

            $data = $pauseform->getData();

            $pause->setWorkentry($workEntry);
            $pause->setKategorie($data['kategorie']);
            $pause->setStartDatum(new \DateTime());

            $entityManager->persist($pause);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('pause.anzeigen'));
        }

        return $this->render('pause/pausestart.html.twig', [
            'pauseform' => $pauseform->createView(),
            'workEntry' => $workEntry
        ]);
    }

    #[Route('/stop', name: 'stop')]
    public function pauseStop(#[CurrentUser] User $user, Request $request, WorkEntryRepository $workEntryRepository, EntityManagerInterface $entityManager): Response
    {
        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        foreach($workEntry->getPauses() as $key => $pause)
        {
            if($pause->getEndeDatum() == null)
            {
                $pause->setEndeDatum(new \DateTime());
                $entityManager->flush();
                break;
            }
        }

        return $this->redirect($this->generateUrl('pause.anzeigen'));
    }
}
