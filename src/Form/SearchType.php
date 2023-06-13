<?php

namespace App\Form;

use App\Form\Model\UserRequestSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('text', null, [
            'label' => false,
            'attr' => [
                'placeholder' => 'search',
                'data-action' => 'search#search',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRequestSearch::class,
        ]);
    }
}
