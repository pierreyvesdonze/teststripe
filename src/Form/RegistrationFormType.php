<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
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
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les 2 mots de passe doivent correspondre',
                'mapped' => false,
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au minimum {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "M'enregistrer",
                'attr' => [
                    'class' => "custom-btn"
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
