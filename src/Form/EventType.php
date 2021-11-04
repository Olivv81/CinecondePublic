<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Evenement;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextareaType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            // ->add('visuel')
            ->add('tarifs')
            ->add('date')
            ->add('dateFin', DateType::class, [
                'placeholder' => '',
                'required' => false,
            ])
            ->add('video')
            ->add('films', EntityType::class, [
                'class' => Film::class,
                // 'query_builder' => function (EntityRepository $er) {
                //     return $er->createQueryBuilder('f')
                //         ->orderBy('f.titre', 'ASC');
                // },

                'choice_label' => 'titre',
                'choice_value' => 'titre',
                'multiple' => "true",
                'required' => false,
            ]);
        // ->add('imageFile', VichImageType::class, [
        //     'required' => false,
        //     'allow_delete' => true,
        //     'delete_label' => '...',
        //     'download_label' => '...',
        //     'download_uri' => true,
        //     'image_uri' => true,
        //     'imagine_pattern' => '...',
        //     'asset_helper' => true,
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
