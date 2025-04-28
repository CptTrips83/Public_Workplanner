<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WorkEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PasswortaendernController extends AbstractController
{
    #[Route('/passwortaendern', name: 'app_passwortaendern')]
    public function aendern(#[CurrentUser] User $user, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, WorkEntryRepository $workEntryRepository, UserPasswordHasherInterface $passEncoder): Response
    {


        $workEntry = $workEntryRepository->findOneBy([
            'user' => $user->getId(),
            'endeDatum' => null
        ]);

        //Formular erstellt mit console -> make:form
        $pwform = $this->createFormBuilder()
            ->add('oldpassword', PasswordType::class,[
                'required' => true,
                'label' => 'Altes Passwort'
            ])
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Neues Passwort'],
                'second_options' => ['label' => 'Neues Passwort Wiederholen']
            ])
            ->add('registrieren', SubmitType::class, [
                'label' => 'Passwort Ändern'
                ])
            ->getForm();

        //Speichert Ergebnis aus Request ins Formular
        $pwform->handleRequest($request);

        if($pwform->isSubmitted()) {

            if($user->getUsername() == "gast") {
                $this->addFlash('passwortaendern','Das Passwort des Gast-Nutzers kann nicht geändert werden!');
                return $this->render('passwortaendern/index.html.twig', [
                    'pwform' => $pwform,
                    'workEntry' => $workEntry
                ]);
            }

            $data = $pwform->getData();            

            if(!$passEncoder->isPasswordValid($user, $data['oldpassword']))
            {
                $this->addFlash('passwortaendern','Altes Passwort ist nicht korrekt!');
            }
            else if($passEncoder->isPasswordValid($user, $data['password']))
            {
                $this->addFlash('passwortaendern','Altes Passwort und neues Passwort dürfen nicht identisch sein!');
            }
            else
            {
                $userRepository->upgradePassword($user,
                    $passEncoder->hashPassword($user, $data['password'])
                );

                return $this->redirect($this->generateUrl('app_uebersicht'));
            }

        }

        return $this->render('passwortaendern/index.html.twig', [
            'pwform' => $pwform,
            'workEntry' => $workEntry
        ]);
    }

}
