<?php

namespace App\Form;

use App\Entity\StyledEntityKind;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StyledEntityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'choice_attr' => function (StyledEntityKind $choice) {
                return [
                    'data-color' => $choice->getColor(),
                    'data-icon' => $choice->getIcon(),
                ];
            },
            'attr' => [
                'data-controller' => 'tomselect',
                'data-tomselect-target' => 'select',
            ],
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
