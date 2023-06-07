<?php

namespace App\Form;

use App\Entity\ContactNL;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactNLType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eMail')
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn'],
                'label' => "Enregistrer mon adresse mail !",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactNL::class,
        ]);
    }
}
