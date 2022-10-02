<?php

namespace App\Form;

use App\Entity\UserRate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductRateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'    => "Titre de l'avis",
                'required' => false
            ])
            ->add('name', TextType::class, [
                'label'    => "Votre nom",
                'required' => false
            ])
            ->add('rate', ChoiceType::class, [
                'label' => 'Note /5',
                'choices' => [
                    '0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ]
            ])
            ->add('comment', TextareaType::class, [
                'label'    => 'Votre commentaire',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider",
                'attr'  => [
                    'class' => "btn custom-btn"
                ]
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRate::class,
        ]);
    }
}
