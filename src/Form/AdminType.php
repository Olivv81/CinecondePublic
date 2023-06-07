<?php

namespace App\Form;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'inscriptionAccueil',
                CheckboxType::class,
                [
                    'label' => "Verrouillage des inscriptions à l'accueil",
                    'required' => false
                ]
            )
            ->add(
                'desincriptionAccueil',
                CheckboxType::class,
                [
                    'label' => "Verrouillage des désinscriptions à l'accueil",
                    'required' => false
                ]
            )
            ->add(
                'inscriptionProjection',
                CheckboxType::class,
                [
                    'label' => "Verrouillage des inscriptions à la projection",
                    'required' => false
                ]
            )
            ->add(
                'desinscriptionProjection',
                CheckboxType::class,
                [
                    'label' => "Verrouillage des désinscriptions à la projection",
                    'required' => false
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
