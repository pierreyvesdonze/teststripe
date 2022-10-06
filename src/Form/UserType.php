<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('phoneNumber', IntegerType::class, [
                'label' => 'Numéro de téléphone (obligatoire)',
                'required' => true
            ])
            ->add('email', TextType::class, [
                'required' => true
            ])
            ->add('addressFirstLine', TextType::class, [
                'label' => 'Adresse (N°, et N° de la voie)',
                'required' => true
            ])
            ->add('addressSecondLine', TextType::class, [
                'label' => 'Adresse seconde ligne (optionnel)',
                'required' => false
            ])
            ->add('addressPostal', TextType::class, [
                'label' => 'Code postal',
                'required' => false
            ])
            ->add('town', TextType::class, [
                'label' => 'ville',
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider",
                'attr' => [
                    'class' => "btn custom-btn"
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
