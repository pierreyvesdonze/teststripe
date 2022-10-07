<?php

namespace App\Form;

use App\Entity\CategoryProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie',
                'required' => false,
                'attr' => [
                    'class' => 'not-anim'
                ]
            ])
            ->add('banner', FileType::class, [
                'label'    => 'Ajouter une bannière (optionnel)',
                'multiple' => false,
                'mapped'   => false,
                'required' => false,
                'attr'     => [
                    'class' => 'category-banner',
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png'
                        ],
                    ])
                ],
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
            'data_class' => CategoryProduct::class,
        ]);
    }
}
