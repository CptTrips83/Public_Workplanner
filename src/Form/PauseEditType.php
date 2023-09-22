<?php

namespace App\Form;

use App\Entity\Pause;
use App\Entity\PauseKategorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PauseEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDatum', TimeType::class, [
                'widget' => 'choice',
                'html5' => true,
                'input' => 'datetime',
                'label' => 'Von'
            ])
            ->add('endeDatum', TimeType::class, [
                'widget' => 'choice',
                'html5' => true,
                'input' => 'datetime',
                'label' => 'Von'
            ])
            ->add('kategorie', EntityType::class, [
                'class' => PauseKategorie::class,
                'label' => 'Kategorie'
            ])
            ->add('edit', SubmitType::class, [
                'label' => 'Speichern'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pause::class,
        ]);
    }
}
