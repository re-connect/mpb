<?php

namespace App\Form;

use App\Entity\BugReport;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class BugReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du Bug',
            ])
            ->add('application', ChoiceType::class, [
                'label' => 'Application',
                'choices' => BugReport::getConstValues(BugReport::APPLICATIONS),
            ])
            ->add('device', ChoiceType::class, [
                'label' => 'Appareil',
                'choices' => BugReport::getConstValues(BugReport::DEVICES),
            ])
            ->add('deviceLanguage', LanguageType::class, [
                'label' => 'Langue Appareil',
                'preferred_choices' => ['fr', 'en', 'es'],
            ])
            ->add('deviceOsVersion', TextType::class, [
                'label' => 'Version OS',
                'required' => false,
            ])
            ->add('browser', ChoiceType::class, [
                'label' => 'Navigateur',
                'choices' => BugReport::getConstValues(BugReport::BROWSERS),
            ])
            ->add('browserVersion', TextType::class, [
                'label' => 'Version Navigateur',
                'required' => false,
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Description du bug rencontré',
            ])
            ->add('history', CKEditorType::class, [
                'label' => 'Historique des actions éxécutées menant au bug (1 ligne = 1 action)',
                'required' => false,
            ])
            ->add('environment', ChoiceType::class, [
                'label' => 'Environnement',
                'choices' => BugReport::getConstValues(BugReport::ENVIRONMENTS),
            ])
            ->add('url', TextType::class, [
                'label' => 'URL sur laquelle a lieu le bug',
                'attr' => [
                    'placeholder' => 'exemple : https://pro.reconnect.fr/families',
                ],
                'required' => false,
            ])
            ->add('accountId', IntegerType::class, [
                'label' => 'ID du compte connecté',
                'required' => false,
            ])
            ->add('accountType', ChoiceType::class, [
                'label' => "Type d'utilisateur",
                'choices' => BugReport::getConstValues(BugReport::ACCOUNT_TYPE),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver):  void
    {
        $resolver->setDefaults([
            'data_class' => BugReport::class,
            'userAgent' => '',
        ]);
    }
}
