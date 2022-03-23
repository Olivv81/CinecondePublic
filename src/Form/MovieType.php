<?php

namespace App\Form;

use App\Form\SeanceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class MovieType extends AbstractType
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
                'required' => \false,
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
            ->add('affichette250');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
