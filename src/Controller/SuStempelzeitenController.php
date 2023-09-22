<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\WorkEntry;
use App\Form\WorkEntryCreateType;
use App\Form\WorkEntryEditType;
use App\Repository\UserRepository;
use App\Repository\WorkEntryRepository;
use App\Tools\Toolkit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/su', name: 'su_stempelzeiten.')]
class SuStempelzeitenController extends AbstractController
{
    #[Route('/auswahl', name: 'auswahl')]
    public function selection(#[CurrentUser] User $user, Request $request, UserRepository $userRepository): Response
    {
        $auswahlform = $this->createFormBuilder()                        
            ->add('user', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (UserRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.nachname', 'ASC');
                }
            ])
            ->add('von', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime',
                'label' => 'Von',
                'data' => new \DateTime(),
                'format' => 'yyyy-MM-dd'
            ])
            ->add('bis', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime',
                'label' => 'Bis',
                'data' => new \DateTime(),
                'format' => 'yyyy-MM-dd'
            ])
            ->add('abrufen', SubmitType::class, [
                'label' => 'Abrufen'
            ])
            ->getForm();

        //Speichert Ergebnis aus Request ins Formular
        $auswahlform->handleRequest($request);

        if($auswahlform->isSubmitted()) {
            
            $data = $auswahlform->getData(); 

            //dump($data);

            $startDatum = (date("Y-m-d", $data['von']->getTimestamp()));
            $endeDatum = (date("Y-m-d", $data['bis']->getTimestamp()));

            return $this->redirect($this->generateUrl('su_stempelzeiten.anzeigen',[
                'userId' => $data['user']->getId(),
                'startDatum' => $startDatum,
                'endeDatum' => $endeDatum
            ]));
        }
        return $this->render('su_stempelzeiten/auswahl.html.twig', [
            'user' => $user,
            'auswahlform' => $auswahlform
        ]);
    }

    #[Route('/anzeigen/{userId}/{startDatum}/{endeDatum}', name: 'anzeigen')]
    public function anzeigen(#[CurrentUser] User $user, Request $request, int $userId, string $startDatum, string $endeDatum, WorkEntryRepository $workEntryRepository, UserRepository $userRepository): Response
    {
        $session = new Session();

        $session->set('userId', $userId);        
        $session->set('startDatum', $startDatum);
        $session->set('endeDatum', $endeDatum);        

        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        $selectedUser = $userRepository->findOneBy([
            'id' => $userId,
            'aktiv' => true
        ]);

        $startDatum .= " 00:00:00";
        $endeDatum .= " 23:00:00";

        $workEntries = $workEntryRepository->findByDateTimeRange($userId, $startDatum, $endeDatum);        

        return $this->render('su_stempelzeiten/index.html.twig', [
            'workEntries' => $workEntries,
            'startDatum' => $startDatum,
            'endeDatum' => $endeDatum,
            'workEntry' => $workEntry,
            'user' => $selectedUser
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(#[CurrentUser] User $user, Request $request, int $id, WorkEntryRepository $workEntryRepository, EntityManagerInterface $entityManager) : Response
    {
        $workEntry = $workEntryRepository->findOneBy([
            'id' => $id
        ]);

        $oldEndeDatum = 0;

        $oldStartDatum = $workEntry->getStartDatum()->getTimestamp();
        if($workEntry->getEndeDatum())$oldEndeDatum = $workEntry->getEndeDatum()->getTimestamp();
        $oldKategorie = $workEntry->getKategorie()->getId();

        $workEntryAktiv = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        //Formular erstellt mit console -> make:form
        $WorkEntryEditType = $this->createForm(WorkEntryEditType::class, $workEntry);

        //Speichert Ergebnis aus Request ins Formular
        $WorkEntryEditType->handleRequest($request);

        if($WorkEntryEditType->isSubmitted() && $WorkEntryEditType->isValid()) {

            $newStartDatum = Toolkit::mergeDateTime($oldStartDatum,$workEntry->getStartDatum()->getTimestamp());
            $newEndeDatum = Toolkit::mergeDateTime($oldStartDatum,$workEntry->getEndeDatum()->getTimestamp());
            $newKategorie = $workEntry->getKategorie()->getId();
            
            Toolkit::checkChange(WorkEntry::class, "StartDatum", date("Y-m-d H:i:s", $oldStartDatum), date("Y-m-d H:i:s", $newStartDatum), $workEntry->getId(),$user->getId(), $entityManager);
            Toolkit::checkChange(WorkEntry::class, "EndeDatum", date("Y-m-d H:i:s", $oldEndeDatum), date("Y-m-d H:i:s", $newEndeDatum), $workEntry->getId(),$user->getId(), $entityManager);
            Toolkit::checkChange(WorkEntry::class, "Kategorie", $oldKategorie, $newKategorie, $workEntry->getId(),$user->getId(), $entityManager);

            $workEntry->setStartDatum(New \DateTime(date("Y-m-d H:i:s",$newStartDatum)));
            $workEntry->setEndeDatum(New \DateTime(date("Y-m-d H:i:s",$newEndeDatum)));

            $workEntryRepository->save($workEntry, true);
               
            $session = new Session();

            $userId = $session->get('userId');
            $startDatum = $session->get('startDatum');
            $endeDatum = $session->get('endeDatum');

            $this->addFlash('su_edit','Eintrag wurde geändert.');

            return $this->redirect($this->generateUrl('su_stempelzeiten.anzeigen',[
                'userId' => $userId,
                'startDatum' => $startDatum,
                'endeDatum' => $endeDatum
            ]));
        }

        

        return $this->render('su_stempelzeiten/edit.html.twig', [            
            'workEntry' => $workEntryAktiv,
            'WorkEntryEditType' => $WorkEntryEditType
        ]);
    }

    #[Route('/archivieren/{id}', name: 'archivieren')]
    public function archivieren(#[CurrentUser] User $user, Request $request, int $id, WorkEntryRepository $workEntryRepository, EntityManagerInterface $entityManager) : Response
    {
        $workEntry = $workEntryRepository->findOneBy([
            'id' => $id
        ]);

        Toolkit::checkChange(WorkEntry::class, "Aktiv", "true", "false", $workEntry->getId(),$user->getId(), $entityManager);

        $workEntry->setAktiv(false);

        $workEntryRepository->save($workEntry, true);

        $session = new Session();

        $userId = $session->get('userId');
        $startDatum = $session->get('startDatum');
        $endeDatum = $session->get('endeDatum');

        $this->addFlash('su_edit','Eintrag wurde archiviert.');

        return $this->redirect($this->generateUrl('su_stempelzeiten.anzeigen',[
                'userId' => $userId,
                'startDatum' => $startDatum,
                'endeDatum' => $endeDatum
            ]));
    }

    #[Route('/erstellen', name: 'erstellen')]
    public function erstellen(#[CurrentUser] User $user, Request $request, WorkEntryRepository $workEntryRepository, EntityManagerInterface $entityManager) : Response
    {
        $workEntryAktiv = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        //Formular erstellt mit console -> make:form
        $workEntryCreateForm = $this->createForm(WorkEntryCreateType::class);

        //Speichert Ergebnis aus Request ins Formular
        $workEntryCreateForm->handleRequest($request);

        if($workEntryCreateForm->isSubmitted() && $workEntryCreateForm->isValid()) {
            $data = $workEntryCreateForm->getData(); 

            foreach($data['user'] as $user)
            {
                $workEntry = new WorkEntry();

                $startDatum = Toolkit::mergeDateTime($data['datum']->getTimestamp(), $data['von']->getTimestamp());
                $endeDatum = Toolkit::mergeDateTime($data['datum']->getTimestamp(), $data['bis']->getTimestamp());

                $workEntry->setStartDatum(new \DateTime(date('Y-m-d H:i:s',$startDatum)));
                $workEntry->setEndeDatum(new \DateTime(date('Y-m-d H:i:s',$endeDatum)));

                $workEntry->setUser($user);
                $workEntry->setKategorie($data['kategorie']);

                $entityManager->persist($workEntry);
                $entityManager->flush();                
                
                Toolkit::checkChange(WorkEntry::class, "Create", "true", "false", $workEntry->getId(),$user->getId(), $entityManager);
            }

            $session = new Session();

            $userId = $session->get('userId');
            $startDatum = $session->get('startDatum');
            $endeDatum = $session->get('endeDatum');

            $this->addFlash('su_edit','Eintrag wurde erstellt.');

            return $this->redirect($this->generateUrl('su_stempelzeiten.anzeigen',[
                'userId' => $userId,
                'startDatum' => $startDatum,
                'endeDatum' => $endeDatum
            ]));
        }

        return $this->render('su_stempelzeiten/create.html.twig',[
            'workEntry' => $workEntryAktiv,
            'workEntryCreateForm' => $workEntryCreateForm
        ]);
    }
}
