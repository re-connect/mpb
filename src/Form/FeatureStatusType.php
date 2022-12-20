<?php

namespace App\Form;

use App\Entity\Feature;
use App\Entity\FeatureStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatureStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', EnumType::class, [
                'label' => false,
                'class' => FeatureStatus::class,
                'choice_label' => static fn (FeatureStatus $choice): string => $choice->value,
                'choice_attr' => fn (FeatureStatus $choice) => [
                    'data-color' => $choice->getColor(),
                    'data-icon' => $choice->getIcon(),
                ],
                'attr' => [
                    'data-controller' => 'tomselect',
                    'data-tomselect-target' => 'select',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feature::class,
        ]);
    }
}
