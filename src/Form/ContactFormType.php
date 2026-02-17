<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'form-control'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'form-control'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'e.g. picard@starfleet.org',
                    'class' => 'form-control'
                ],
                'row_attr' => [
                    'class' => 'col-md-6',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'col-md-12',
                ],
                'attr' => [
                    'placeholder' => 'Votre demande',
                    'class' => 'form-control',
                    'rows' => '6',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
