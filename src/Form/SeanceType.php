<?php

namespace App\Form;

use App\Entity\Seance;
use App\Form\HoraireType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('projection', TextType::class, [
                'label' => 'Version : ',
            ])
            ->add('vo', CheckboxType::class, [
                'label' => 'VO',
                'required' => false,
            ])
            ->add('horaires', CollectionType::class, [
                'entry_type' => HoraireType::class,
                'entry_options' => ['label' => false],
                'allow_add' => false,
                'allow_delete' => false,
                'label' => false,
            ])

            ->add('addHoraire', SubmitType::class, [
                'label' => 'Enregistrer et ajouter un horaire',
                'attr' => ['class' => 'btn'],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}
