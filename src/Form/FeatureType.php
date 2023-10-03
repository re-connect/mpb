<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Feature;
use App\Entity\Requester;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => false,
                'attr' => [
                    'placeholder' => 'title',
                ],
                'empty_data' => '',
                'row_attr' => ['class' => 'form-floating'],
            ])
            ->add('application', StyledEntityType::class, [
                'class' => Application::class,
                'placeholder' => 'application',
            ])
            ->add('center', TextType::class, [
                'required' => false,
                'label' => false,
                'autocomplete' => true,
                'tom_select_options' => [
                    'create' => true,
                    'options' => $options['centerValues'],
                    'placeholder' => $this->translator->trans('choose_center'),
                ],
            ])
            ->add('requestedBy', StyledEntityType::class, [
                'class' => Requester::class,
                'placeholder' => 'requester',
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'description',
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feature::class,
            'centerValues' => [],
        ]);
    }
}
