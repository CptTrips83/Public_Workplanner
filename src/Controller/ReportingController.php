<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WorkEntryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Dompdf\Dompdf;

#[Route('/reporting', name: 'reporting.')]
class ReportingController extends AbstractController
{
    #[Route('/auswahl', name: 'auswahl')]
    public function auswahl(Request $request, WorkEntryRepository $workEntryRepository): Response
    {
        $auswahlform = $this->createFormBuilder()                        
            ->add('user', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (UserRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.nachname', 'ASC');
                },
                'expanded' => true,
                'multiple' => true
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

            $reportData = array();

            foreach($data['user'] as $key => $user)
            {
                $reportData[$user->getId()] = $this->initUserDataArray($user);

                $startDatum = \date("Y-m-d H:i:s", $data['von']->getTimestamp());
                $endeDatum = \date("Y-m-d H:i:s", $data['bis']->getTimestamp());

                $workEntries = $workEntryRepository->findByDateTimeRange($user->getId(), $startDatum, $endeDatum);

                foreach($workEntries as $key => $workEntry)
                {
                    if($workEntry->getKategorie()->getName() == "Arbeit") $reportData[$user->getId()]["arbeitszeit"] += $workEntry->getArbeitsZeit();
                    if($workEntry->getKategorie()->getName() == "Urlaub") $reportData[$user->getId()]["urlaubszeit"] += $workEntry->getArbeitsZeit();
                    if($workEntry->getKategorie()->getName() == "Krank") $reportData[$user->getId()]["krankzeit"] += $workEntry->getArbeitsZeit(); 

                    $reportData[$user->getId()]["bezahltepause"] += $workEntry->getPausenZeitBezahlt();
                    $reportData[$user->getId()]["unbezahltepause"] += $workEntry->getPausenZeitUnbezahlt();
                }
            }

            $html =  $this->renderView('reporting/report.html.twig', [
                'reportData' => $reportData,
                'startDatum' => $startDatum,
                'endeDatum' => $endeDatum
                ]);

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
         
            return new Response (
                $dompdf->stream('resume', ["Attachment" => false]),
                Response::HTTP_OK,
                ['Content-Type' => 'application/pdf']
        );
        }

        return $this->render('reporting/index.html.twig', [
            'auswahlform' => $auswahlform,
        ]);
    }

    private function initUserDataArray(User $user) : array 
    {
        $userdata = array();

        $userdata["userId"] = $user->getId();
        $userdata["vorname"] = $user->getVorname();
        $userdata["nachname"] = $user->getNachname();
        $userdata["arbeitszeit"] = 0;
        $userdata["urlaubszeit"] = 0;
        $userdata["krankzeit"] = 0;        
        $userdata["bezahltepause"] = 0;
        $userdata["unbezahltepause"] = 0;

        return $userdata;
    }
}
