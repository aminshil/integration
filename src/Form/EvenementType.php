<?php

namespace App\Form;
use App\Entity\Evenement;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;
class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $evenement= new Evenement();
        $currentDate = new \DateTime();
        $builder
        ->add('nom')
        ->add('description')
        ->add('latitude', HiddenType::class)
        ->add('longitude', HiddenType::class)
        ->add('date_debut', DateTimeType::class, [
            'widget' => 'single_text',
        ])
        ->add('date_fin', DateTimeType::class, [
            'widget' => 'single_text',
        ])
        ->add('objectif')
        ->add('formation', ChoiceType::class, [
            'choices' => [
                'OUI' => true,
                'NON' => false,
            ],
        ])
        ->add('locationtext')
        ->add('image', FileType::class,['mapped'=>false,'required' => true ,'constraints' => [
            new Assert\NotBlank([
                'message' => 'Veuillez sélectionner une image.',
            ]),
            new Assert\File([
                'mimeTypes' => ['image/jpeg', 'image/png'],
                'mimeTypesMessage' => 'Veuillez sélectionner un fichier image valide (JPEG, PNG).',
            ]),
        ],])
        ->add('Enregistrer',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
