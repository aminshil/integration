<?php

namespace App\Form;
use App\Entity\CategorieDepot;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Depots;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\DataTransformerInterface;

class DepotsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom')
            ->add('Adresse')
            ->add('Etat')
            ->add('Image', FileType::class,[
            ])
           
            ->add('categorie', EntityType::class, [
                'class' => CategorieDepot::class, // Utilisez la classe Categorie directement
                'choice_label' => 'Nom', // Assurez-vous que l'entité Categorie a bien une propriété Nom
                'placeholder' => 'Select a Categorie', // Modifiez le texte du placeholder si nécessaire
                'required' => true, // Maintenez cette option si la sélection de la catégorie est obligatoire
            ])
            ->get('Image')->addModelTransformer(new class() implements DataTransformerInterface {
                public function transform($value)
                {
                    return null; // transforme l'entité en une chaîne pour le formulaire
                }
    
                public function reverseTransform($value)
                {
                    return $value; // transforme la chaîne en une instance de File
                }
            });
          
        

        }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Depots::class,
        ]);
    }
}
