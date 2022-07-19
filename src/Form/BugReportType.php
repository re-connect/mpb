<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\BugReport;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BugReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du Bug',
                'required' => false,
                'attr' => [
                    'placeholder' => 'sdasd',
                ],
            ])
            ->add('application', EntityType::class, [
                'label' => 'Application',
                'class' => Application::class,
                'choice_attr' => function (Application $choice) {
                    return [
                        'data-color' => $choice->getColor(),
                        'data-icon' => $choice->getIcon(),
                    ];
                },
                'attr' => [
                    'data-controller' => 'tomselect',
                    'data-tomselect-target' => 'select',
                ],
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Description du bug rencontré',
            ])
            ->add('url', TextType::class, [
                'label' => 'URL',
                'attr' => [
                    'placeholder' => 'exemple : https://pro.reconnect.fr/families',
                ],
                'required' => false,
            ])
            ->add('accountId', IntegerType::class, [
                'label' => 'ID compte',
                'required' => false,
            ])
            ->add('accountType', ChoiceType::class, [
                'label' => "Type d'utilisateur",
                'choices' => BugReport::getConstValues(BugReport::ACCOUNT_TYPE),
                'attr' => [
                    'data-controller' => 'tomselect',
                    'data-tomselect-target' => 'select',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BugReport::class,
        ]);
    }
}
