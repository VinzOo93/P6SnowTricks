<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Type;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du trick',
                'attr' => [
                    'maxlength' => 64,
                    'minlength' => 6,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir le nom du trick'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'mapped' => true,
                'required' => false,
            ])
            ->add('type', null, [
                'label' => 'Groupe',
                'mapped' => true,
                'required' => true,
            ])
            ->add('photos', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'empty_data' => '',
//                'constraints' => [
//                    new File([
//                        'maxSize' => '1024k',
//                        'mimeTypes' => [
//                            'application/jpeg',
//                            'application/jpg',
//                            'application/png',
//                        ],
//                        'mimeTypesMessage' => 'Merci de choisir une format JPG ou PNG inférieur à 1024ko'
//                    ])
//                ]
            ])
            ->add('videos', UrlType::class, [
                'label' => 'URL Video',
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
