<?php

namespace App\Form;

use App\Entity\CategoryProduct;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'required' => true
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true
            ])
            ->add('description_short', TextType::class, [
                'label' => 'Description courte'
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'required' => true
            ])
            ->add('image', FileType::class, [
                'label'    => 'Ajouter une image',
                'multiple' => false,
                'mapped'   => false,
                'required' => false,
                'attr'     => [
                    'class' => 'add-img-gallery',
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
            ->add('categoryProduct', EntityType::class, [
                'label' => 'Catégorie de produit',
                'class' => CategoryProduct::class,
                'choice_label' => 'name',
                'constraints' => array(
                    new NotBlank(array('message' => 'Sélectionner une catégorie'))
                )
            ])
            ->add('isActiv', ChoiceType::class, [
                'label' => 'Activer le produit',
                'choices' => [
                    'Oui' => 1,
                    'Non' => 0
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider",
                'attr' => [
                    'class' => "btn custom-btn"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
