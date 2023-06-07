<?php

namespace App\Form;

use App\Entity\NewsLetter;
use PhpParser\Node\Stmt\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NewsLetterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextareaType::class, [
                'label' => "Titre",

            ])
            ->add("message", TextareaType::class, [
                'label' => "Message d'en-tête",
                'required' => false,
            ])
            ->add('event',  CheckboxType::class, [
                'label' => "Intégrer les évènements",
                'required' => false,
            ])
            ->add("films",  CheckboxType::class, [
                'label' => "Intégrer les films à l'affiche",
                'required' => false,
            ])
            ->add('docs',  CheckboxType::class, [
                'label' => "Intégrer les liens de téléchargement de l'affiche et du dépliant",
                'required' => false,
            ])
            ->add("test", CheckboxType::class, [
                'attr' => ['checked' => 'true'],
                'label' => "Réaliser un test à l'adresse ci-dessous",
                'required' => false,
            ])
            ->add('emailTest', EmailType::class, [
                'label' => "Adresse mail pour le test",
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn'],
                'label' => "Envoyer !",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewsLetter::class,
        ]);
    }
}
