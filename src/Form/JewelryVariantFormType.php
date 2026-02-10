<?php

namespace App\Form;

use App\Enum\JewelryColor;
use App\Entity\JewelryVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class JewelryVariantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('color', ChoiceType::class, [
                'label' => 'Couleur',
                'choices' => [
                    'Argent' => JewelryColor::SILVER->value,
                    'Or' => JewelryColor::GOLD->value,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('priceCents', MoneyType::class, [
                'label' => 'Prix unitaire',
                'currency' => 'EUR',
                'divisor' => 100,
                'required' => true
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité'
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 4,
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'label' => false,
                'delete_label' => 'Supprimer l\'image',
                'download_uri' => false,
                'attr' => [
                    'class' => 'form-control mb-2'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => JewelryVariant::class,
        ]);
    }
}
