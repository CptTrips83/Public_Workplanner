<?php

namespace App\Controller;

use App\Entity\Pause;
use App\Entity\PauseKategorie;
use App\Entity\User;
use App\Form\PauseCreateType;
use App\Form\PauseEditType;
use App\Form\PauseKategorieType;
use App\Repository\PauseKategorieRepository;
use App\Repository\PauseRepository;
use App\Repository\UserRepository;
use App\Repository\WorkEntryRepository;
use App\Tools\Toolkit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/su/pausen', name: 'su_pausenzeiten.')]
class SuPausenController extends AbstractController
{
    #[Route('/anzeigen/{id}', name: 'anzeigen')]
    public function anzeigen(#[CurrentUser] User $user, Request $request, int $id, PauseRepository $pauseRepository, UserRepository $userRepository): Response
    {
        $session = new Session();

        $session->set('id', $id);

        $pauses = $pauseRepository->findBy([
            'workentry' => $id,
            'aktiv' => true
        ]);        

        return $this->render('su_pausen/index.html.twig', [
            'pauses' => $pauses,
            'id' => $id
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(#[CurrentUser] User $user, Request $request, int $id, PauseRepository $pauseRepository, WorkEntryRepository $workEntryRepository, EntityManagerInterface $entityManager) : Response
    {
        $pause = $pauseRepository->findOneBy([
            'id' => $id
        ]);

        $oldStartDatum = $pause->getStartDatum()->getTimestamp();
        $oldEndeDatum = $pause->getEndeDatum()->getTimestamp();
        $oldKategorie = $pause->getKategorie()->getId();

        $workEntryAktiv = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        //Formular erstellt mit console -> make:form
        $pauseEditForm = $this->createForm(PauseEditType::class, $pause);

        //Speichert Ergebnis aus Request ins Formular
        $pauseEditForm->handleRequest($request);

        if($pauseEditForm->isSubmitted() && $pauseEditForm->isValid()) {

            $newStartDatum = Toolkit::mergeDateTime($oldStartDatum,$pause->getStartDatum()->getTimestamp());
            $newEndeDatum = Toolkit::mergeDateTime($oldStartDatum,$pause->getEndeDatum()->getTimestamp());
            $newKategorie = $pause->getKategorie()->getId();
            
            Toolkit::checkChange(Pause::class, "StartDatum", date("Y-m-d H:i:s", $oldStartDatum), date("Y-m-d H:i:s", $newStartDatum), $pause->getId(),$user->getId(), $entityManager);
            Toolkit::checkChange(Pause::class, "EndeDatum", date("Y-m-d H:i:s", $oldEndeDatum), date("Y-m-d H:i:s", $newEndeDatum), $pause->getId(),$user->getId(), $entityManager);
            Toolkit::checkChange(Pause::class, "Kategorie", $oldKategorie, $newKategorie, $pause->getId(),$user->getId(), $entityManager);

            $pause->setStartDatum(New \DateTime(date("Y-m-d H:i:s",$newStartDatum)));
            $pause->setEndeDatum(New \DateTime(date("Y-m-d H:i:s",$newEndeDatum)));

            $pauseRepository->save($pause, true);
               
            $session = new Session();

            $id = $session->get('id');

            $this->addFlash('su_edit','Eintrag wurde geändert.');

            return $this->redirect($this->generateUrl('su_pausenzeiten.anzeigen',[
                'id' => $id
            ]));
        }

        

        return $this->render('su_pausen/edit.html.twig', [            
            'workEntry' => $workEntryAktiv,
            'pauseEditForm' => $pauseEditForm
        ]);
    }

    #[Route('/archivieren/{id}', name: 'archivieren')]
    public function archivieren(#[CurrentUser] User $user, Request $request, int $id, PauseRepository $pauseRepository, EntityManagerInterface $entityManager) : Response
    {
        $pause = $pauseRepository->findOneBy([
            'id' => $id
        ]);

        Toolkit::checkChange(WorkEntry::class, "Aktiv", "true", "false", $pause->getId(),$user->getId(), $entityManager);

        $pause->setAktiv(false);

        $pauseRepository->save($pause, true);

        $session = new Session();

        $id = $session->get('id');

        $this->addFlash('su_edit','Eintrag wurde archiviert.');

        return $this->redirect($this->generateUrl('su_pausenzeiten.anzeigen',[
                'id' => $id
            ]));
    }

    #[Route('/showkategorie', name: 'show_kategorie')]
    public function showKategorie(#[CurrentUser] User $user, Request $request, WorkEntryRepository $workEntryRepository, PauseKategorieRepository $pauseKategorieRepository) : Response
    {
        $workEntryAktiv = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        
        $pauseKategories = $pauseKategorieRepository->findAll();

        return $this->render('su_pausen/anzeigen_kategorie_pause.html.twig', [            
            'workEntry' => $workEntryAktiv,          
            'pauseKategories' => $pauseKategories
        ]);
    }

    #[Route('/createkategorie', name: 'create_kategorie')]
    public function editKategorie(#[CurrentUser] User $user, Request $request, WorkEntryRepository $workEntryRepository, PauseKategorieRepository $pauseKategorieRepository, EntityManagerInterface $entityManager) : Response
    {
        $workEntryAktiv = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        $pauseKategorie = new PauseKategorie();

        $pauseKategorie->setName('Pause');

        //Formular erstellt mit console -> make:form
        $pauseKategorieForm = $this->createForm(PauseKategorieType::class, $pauseKategorie);

        //Speichert Ergebnis aus Request ins Formular
        $pauseKategorieForm->handleRequest($request);

        if($pauseKategorieForm->isSubmitted() && $pauseKategorieForm->isValid()) {

            $pauseKategorieRepository->save($pauseKategorie, true);

            $this->addFlash('su_change_aktiv_kategorie','Kategorie wurde angelegt.');

            return $this->redirect($this->generateUrl('su_pausenzeiten.show_kategorie'));
        }        

        return $this->render('su_pausen/create_kategorie.html.twig', [            
            'workEntry' => $workEntryAktiv,
            'pauseKategorieForm' => $pauseKategorieForm
        ]);
    }

    #[Route('/changeAktivKategorie/{id}', name: 'change_aktiv_kategorie')]
    public function changeAktivKategorie(#[CurrentUser] User $user, Request $request, int $id, EntityManagerInterface $entityManager, WorkEntryRepository $workEntryRepository, PauseKategorieRepository $pauseKategorieRepository) : Response
    {
        $workEntryAktiv = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        $pauseKategorie = $pauseKategorieRepository->findOneBy([
            'id' => $id
        ]);

        $alterWert = $pauseKategorie->isAktiv();
        $neuerWert = !$alterWert;

        $pauseKategorie->setAktiv($neuerWert);

        Toolkit::checkChange(PauseKategorie::class, "aktiv", $alterWert, $neuerWert, $pauseKategorie->getId(),$user->getId(), $entityManager);

        $entityManager->persist($pauseKategorie);
        $entityManager->flush();

        $this->addFlash('su_change_aktiv_kategorie','Kategorie wurde geändert.');

        return $this->redirect($this->generateUrl('su_pausenzeiten.show_kategorie'));
    }

    #[Route('/erstellen/{id}', name: 'erstellen')]
    public function erstellen(#[CurrentUser] User $user, int $id, Request $request, WorkEntryRepository $workEntryRepository, EntityManagerInterface $entityManager) : Response
    {
        $workEntryAktiv = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        //Formular erstellt mit console -> make:form
        $pauseCreateForm = $this->createForm(PauseCreateType::class);

        //Speichert Ergebnis aus Request ins Formular
        $pauseCreateForm->handleRequest($request);

        if($pauseCreateForm->isSubmitted() && $pauseCreateForm->isValid()) {
            $data = $pauseCreateForm->getData(); 

            $pause = new Pause();

            $workEntry = $workEntryRepository->findOneBy([
                'id' => $id
            ]);

            $startDatum = Toolkit::mergeDateTime($workEntry->getStartDatum()->getTimestamp(), $data['von']->getTimestamp());
            $endeDatum = Toolkit::mergeDateTime($workEntry->getStartDatum()->getTimestamp(), $data['bis']->getTimestamp());
            
            $pause->setStartDatum(new \DateTime(date('Y-m-d H:i:s',$startDatum)));
            $pause->setEndeDatum(new \DateTime(date('Y-m-d H:i:s',$endeDatum)));
                
            $pause->setKategorie($data['kategorie']);
            $pause->setWorkentry($workEntry);

            $entityManager->persist($pause);
            $entityManager->flush();                
                
            Toolkit::checkChange(Pause::class, "Create", "true", "false", $pause->getId(),$user->getId(), $entityManager);
            
            $this->addFlash('su_edit','Eintrag wurde erstellt.');

            $session = new Session();

            $userId = $session->get('userId');
            $startDatum = $session->get('startDatum');
            $endeDatum = $session->get('endeDatum');

            return $this->redirect($this->generateUrl('su_stempelzeiten.anzeigen',[
                'userId' => $userId,
                'startDatum' => $startDatum,
                'endeDatum' => $endeDatum
            ]));
        }

        return $this->render('su_pausen/create.html.twig',[
            'workEntry' => $workEntryAktiv,
            'pauseCreateForm' => $pauseCreateForm
        ]);
    }

}
