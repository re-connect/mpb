<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Bug;
use App\Entity\UserKind;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BugType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'bug_title',
                'required' => false,
                'attr' => [
                    'placeholder' => 'bug_title',
                ],
                'empty_data' => '',
            ])
            ->add('application', StyledEntityType::class, [
                'required' => true,
                'class' => Application::class,
                'placeholder' => 'application',
            ])
            ->add('userKind', StyledEntityType::class, [
                'class' => UserKind::class,
                'placeholder' => 'user_kind',
            ])
            ->add('url', TextType::class, [
                'label' => 'url_extended',
                'attr' => [
                    'placeholder' => 'url_extended',
                ],
                'required' => false,
            ])
            ->add('accountId', IntegerType::class, [
                'label' => 'accountId',
                'required' => false,
                'attr' => [
                    'placeholder' => 'accountId',
                ],
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'bug_description',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bug::class,
        ]);
    }
}
