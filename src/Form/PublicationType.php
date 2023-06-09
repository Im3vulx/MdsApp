<?php

namespace App\Form;

use App\Entity\Work;
use App\Entity\Category;
use App\Entity\Publication;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la publication'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ])->add('logement', LogementType::class, [
                'label' => 'Logement',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('work', EntityType::class, [
                'class' => Work::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.title', 'ASC');
                },
                'choice_label' => 'title',
                'label' => 'Emploi',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('photo', DropzoneType::class, [
                'label' => 'Photo de la publication',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10000k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/x-png',
                            'image/x-jpg',
                            'image/x-jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid png or jpg or jpeg',
                    ])
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.title', 'ASC');
                },
                'choice_label' => 'title',
                'label' => 'Catégorie',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('adresse', AdresseType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])           
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
