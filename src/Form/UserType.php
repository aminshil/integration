<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'societe' => 'ROLE_SOCIETE',
                    'admin' => 'ROLE_ADMIN',
                    'user' => 'ROLE_USER',
                ],
                'mapped' => false,
                'label' => 'Select Role',
                'required' => false,
            ])
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('image', FileType::class, [
                'mapped' => true,
                'required' => false,
                'label' => 'Profile Image',
                'attr' => ['accept' => 'image/*'],
                'data_class' => null, // Set data_class to null for image field
            ])
            ->add('dateDeNaissance', DateType::class, [
                'widget' => 'single_text',
                // You can customize date options as needed
            ])
            ->add('isVerified')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
