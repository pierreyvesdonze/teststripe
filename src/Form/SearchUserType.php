<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchType', ChoiceType::class, [
                'label'   => false,
                'choices' => [
                    'Id'    => 'id',
                    'Email' => 'email'
                ],
                'attr' => [
                    'class' => 'admin-search-type'
                ]
            ])
            ->add('query', TextType::class, [
                'label'    => false,
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Entrer une valeur',
                    'class'       => 'admin-search-value'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Go",
                'attr'  => [
                    'class' => "btn custom-btn"
                ]
            ]);
        ;
    }
}
