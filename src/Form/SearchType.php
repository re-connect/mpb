<?php

namespace App\Form;

use App\Entity\Tag;
use App\Form\Model\UserRequestSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('text', TextType::class, [
            'label' => false,
            'attr' => [
                'placeholder' => 'Rechercher',
                'data-action' => 'search#search',
                'class' => 'large-input',
            ],
            'empty_data' => '',
        ])
            ->add('tags', StyledEntityType::class, [
                'class' => Tag::class,
                'multiple' => true,
                'expanded' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRequestSearch::class,
        ]);
    }
}
