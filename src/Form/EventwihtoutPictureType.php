<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EventwihtoutPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextareaType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('tarifs')
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateType::class, [
                'placeholder' => '',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('video')
            ->add('videoVimeo', TextType::class, [
                'attr' => ['placeholder' => 'https://vimeo.com/xxxxxxxx'],
                'required' => \false,
            ])
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
                'attr' => ['multiple data-multi-select-plugin' => ''],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => true,
                'delete_label' => '...',
                'download_label' => '...',
                'download_uri' => true,
                'image_uri' => true,
                'imagine_pattern' => '...',
                'asset_helper' => true,

            ]);
        // ->add('save', SubmitType::class, [
        //     'attr' => ['class' => 'btn']
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
