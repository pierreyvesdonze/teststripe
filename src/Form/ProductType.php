<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference')
            ->add('name')
            ->add('description')
            ->add('description_short')
            ->add('price')
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
            ->add('orders')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
