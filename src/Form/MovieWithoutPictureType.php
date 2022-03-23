<?php

namespace App\Form;

use App\Entity\Film;
use App\Form\SeanceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class MovieWithoutPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('realisateurs')
            ->add('acteurs')
            ->add('anneeproduction', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateSortie', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('duree', TimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('genrePrincipal')
            ->add('nationalite')
            ->add('synopsis', TextareaType::class)
            ->add('affichette')
            ->add('video', TextType::class, [
                'required' => false,
            ])
            ->add('videoYT', TextType::class, [
                'required' => false,
            ])
            ->add('videoVimeo', TextType::class, [
                'required' => false,
            ])
            ->add('visaNumber')
            ->add('idFilm')
            ->add('classification')
            ->add('affichette250')
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => '...',
                'download_label' => '...',
                'download_uri' => true,
                'image_uri' => true,
                'imagine_pattern' => '...',
                'asset_helper' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
