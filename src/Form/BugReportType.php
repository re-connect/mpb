<?php

namespace App\Form;

use App\Entity\BugReport;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
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
            ->add('application', ChoiceType::class, [
                'label' => 'Application',
                'choices' => BugReport::getConstValues(BugReport::APPLICATIONS),
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BugReport::class,
        ]);
    }
}
