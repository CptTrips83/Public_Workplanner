<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserRoles;
use App\Entity\WorkEntry;
use App\Repository\WorkEntryRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class RegistrierungController extends AbstractController
{
    #[Route('/reg', name: 'reg')]
    public function reg(#[CurrentUser] User $user, Request $request, EntityManagerInterface $entityManager, WorkEntryRepository $workEntryRepository, UserPasswordHasherInterface $passEncoder): Response
    {       
        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        //Formular erstellt mit console -> make:form
        $regform = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'label' => 'Benutzername',
                'required' => true])
            ->add('vorname', TextType::class, [
                'label' => 'Vorname',
                'required' => true])
            ->add('nachname', TextType::class, [
                'label' => 'Nachname',
                'required' => true])
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Passwort'],
                'second_options' => ['label' => 'Passwort Wiederholen']
            ])->add('role', EntityType::class, [
                'class' => UserRoles::class,
                'label' => 'Rolle'
            ])
            ->add('registrieren', SubmitType::class, [
                'label' => 'Benutzer erstellen'
                ])
            ->getForm();

        //Speichert Ergebnis aus Request ins Formular
        $regform->handleRequest($request);

        if($regform->isSubmitted()) {
            
            $data = $regform->getData();

            // Entity erstellen
            $user = new User();

            $user->setUsername($data['username']);  
            $user->setVorname($data['vorname']); 
            $user->setNachname($data['nachname']); 
            $user->setRoles([$data['role']->getSecurityName()]);           

            $user->setPassword(
                $passEncoder->hashPassword($user, $data['password'])
            );

            $user->setAktiv('1');

            try
            {
                // Wenn das Formular gespeichert wurde dann Werte in DB schreiben und Redirect
                $entityManager->persist($user);
                $entityManager->flush();

            }
            catch(UniqueConstraintViolationException $ex)
            {
                return $this->render('registrierung/index.html.twig', [
                    'regform' => $regform->createView(),
                    'errorMsg' => 'Benuter existiert bereits!',
                    'workEntry' => $workEntry
                ]);
            }
            return $this->redirect($this->generateUrl('app_uebersicht'));
        }

        return $this->render('registrierung/index.html.twig', [
            'regform' => $regform->createView(),
            'errorMsg' => '',
            'workEntry' => $workEntry
        ]);
    }
}
