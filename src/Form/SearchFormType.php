<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('departure', TextType::class,
                [
                'attr' => [
                    'placeholder' => 'Departure',
                ]
            ])
            ->add('arrival', TextType::class,
                [
                'attr' => [
                    'placeholder' => 'Arrival',
                ]
            ])
            ->add('submit', SubmitType::class,
            [
              'label' => 'Search',
              'attr' => [
                  'class' => 'btn btn btn-primary',
              ]
            ])
        ;
    }

    /*public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }*/
}
