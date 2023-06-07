<?php

namespace App\Form;

use PhpParser\Node\Stmt\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RelanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet', TextType::class, [])
            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 10],
            ])
            ->add('commission', ChoiceType::class, [
                'label' => 'Envoyer à la commission :',
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'choices'  => [
                    'Accueil' => 'accueil',
                    'Projection' => 'projection',
                ],
            ])
            ->add('listeDesSeances', ChoiceType::class, [
                'label' => 'Liste des séances :',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'liste avec toutes les séances' => true,
                    'Liste avec les séances non pourvues uniquement' => false,
                ],
            ])

            // ->add('accueil', CheckboxType::class, [
            //     'label' => 'Envoyer à la commission accueil.',
            // ])
            // ->add('projection', CheckboxType::class, [
            //     'label' => 'Envoyer à la commission projection.'
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
