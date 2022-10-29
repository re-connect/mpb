<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Feature;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => false,
                'attr' => [
                    'placeholder' => 'title',
                ],
            ])
            ->add('application', StyledEntityType::class, [
                'class' => Application::class,
                'placeholder' => 'application',
            ])
            ->add('center', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'center',
                    'data-controller' => 'tomselect',
                    'data-tomselect-target' => 'select',
                ],
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'description',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feature::class,
        ]);
    }
}
