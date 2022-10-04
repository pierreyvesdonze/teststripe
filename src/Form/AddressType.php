<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label'    => "Titre de l'addresse"
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label'    => 'Prénom'
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label'    => 'Nom'
            ])
            ->add('addressFirstLine', TextType::class, [
                'required' => true,
                'label'    => 'Adresse (N° et nom de la voie)'
            ])
            ->add('addressSecondLine', TextType::class, [
                'required' => false,
                'label'    => 'Adresse seconde ligne (optionnel)'
            ])
            ->add('addressPostal', IntegerType::class, [
                'required' => true,
                'label'    => 'Code postal'
            ])
            ->add('addressTown', TextType::class, [
                'required' => true,
                'label'    => 'Ville'
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider",
                'attr'  => [
                    'class' => "btn custom-btn"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
