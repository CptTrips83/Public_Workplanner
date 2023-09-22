<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\WorkEntryKategorie;
use App\Repository\UserRepository;
use App\Repository\WorkEntryKategorieRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkEntryCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('datum', DateType::class, [
            'widget' => 'single_text',
            'html5' => true,
            'input' => 'datetime',
            'label' => 'Datum',
            'data' => new \DateTime()
        ])
        ->add('von', TimeType::class, [
            'widget' => 'single_text',
            'html5' => true,
            'input' => 'datetime',
            'label' => 'Von',
            'data' => new \DateTime()
        ])
        ->add('bis', TimeType::class, [
            'widget' => 'single_text',
            'html5' => true,
            'input' => 'datetime',
            'label' => 'Bis',
            'data' => new \DateTime()
        ])
        ->add('user', EntityType::class, [
            'class' => User::class,
            'query_builder' => function (UserRepository $er): QueryBuilder {
                return $er->createQueryBuilder('u')
                    ->where('u.aktiv = 1')
                    ->orderBy('u.nachname', 'ASC');
            },
            'expanded' => true,
            'multiple' => true
        ])
        ->add('kategorie', EntityType::class, [
            'class' => WorkEntryKategorie::class,
            'query_builder' => function (WorkEntryKategorieRepository $er): QueryBuilder {
                return $er->createQueryBuilder('k')
                    ->where('k.aktiv = 1')
                    ->orderBy('k.name', 'ASC');
            }
            
        ])
        ->add('abrufen', SubmitType::class, [
            'label' => 'Eintrag erstellen'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
