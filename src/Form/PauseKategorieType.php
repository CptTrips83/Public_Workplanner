<?php

namespace App\Form;

use App\Entity\PauseKategorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PauseKategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true
            ])
            ->add('bezahlt', CheckboxType::class, [
                'label' => 'Bezahlte Pause',
                'required' => false
            ])
            ->add('edit', SubmitType::class, [
                'label' => 'Anlegen'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PauseKategorie::class,
        ]);
    }
}
