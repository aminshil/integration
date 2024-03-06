<?php

namespace App\Form;

use App\Entity\Demande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('demande')
            ->add('nameofobj')
            ->add('stateofdem')
            ->add('Category', EntityType::class, [
                'class' => 'App\Entity\Category', // Replace with the actual namespace of your Author entity
                'choice_label' => 'typeofcat', // Assuming Author entity has a method getFullName() that returns the author's full name
                'placeholder' => 'Select a type', // Optional, adds an empty option at the top
                'required' => true, // Set to true if the author selection is mandatory
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demande::class,
        ]);
    }
}
