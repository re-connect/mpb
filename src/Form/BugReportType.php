<?php

namespace App\Form;

use App\Entity\BugReport;
use App\Entity\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class BugReportType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du Bug Report'
            ])
            ->add('application', ChoiceType::class, [
                'label' => 'Application concernée par le bug',
                'choices' => BugReport::getConstValues(BugReport::APPLICATIONS)
            ])
            ->add('device', TextType::class, [
                'label' => 'Appareil',
                'attr' => [
                    'placeholder' => 'Ordinateur, smartphone, tablette...'
                ]
            ])
            ->add('deviceLanguage', TextType::class, [
                'label' => 'Langue de l\'appareil',
                'attr' => [
                    'placeholder' => 'Quelle est la langue configurée sur l\'appareil ?'
                ]
            ])
            ->add('deviceOs', TextType::class, [
                'label' => 'Système d\'exploitation de l\'appareil',
                'attr' => [
                    'placeholder' => 'Windows, MacOS, Android, iOS...'
                ]
            ])
            ->add('deviceOsVersion', TextType::class, [
                'label' => 'Version du système d\'exploitation de l\'appareil',
                'required' => false
            ])
            ->add('browser', TextType::class, [
                'label' => 'Navigateur internet utilisé',
                'attr' => [
                    'placeholder' => 'Chrome, Firefox, Internet Explorer, Safari, Navigateur du téléphone...'
                ]
            ])
            ->add('browserVersion', TextType::class, [
                'label' => 'Version du navigateur internet utilisé',
                'required' => false
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Description du bug rencontré',
            ])
            ->add('history', CKEditorType::class, [
                'label' => 'Historique des actions éxécutées menant au bug (1 ligne = 1 action)',
            ])
            ->add('environment', ChoiceType::class, [
                'label' => 'Environnement sur lequel a lieu le bug',
                'choices' => BugReport::getConstValues(BugReport::ENVIRONMENTS)
            ])
            ->add('url', TextType::class, [
                'label' => 'URL sur laquelle a lieu le bug',
                'attr' => [
                    'placeholder' => 'exemple : https://pro.reconnect.fr/families'
                ],
                'required' => false
            ])
            ->add('accountId', IntegerType::class, [
                'label' => 'ID du compte concerné par le bug'
            ])
            ->add('accountType', ChoiceType::class, [
                'label' => 'Type de profil utilisé lors du bug',
                'choices' => BugReport::getConstValues(BugReport::ACCOUNT_TYPE)
            ])
            ->add('itemId', IntegerType::class, [
                'label' => 'Si pertinent, ID de l\'item problématique',
                'attr' => [
                    'placeholder' => 'exemple : id du bénéficiaire, id du ménage, id du document...'
                ],
                'required' => false
            ])
            ->add('otherInfo', TextType::class, [
                'label' => 'Autre information pouvant aider à la reproduction du bug',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'label' => 'Je considère ce bug comme...',
                'label_attr' => ['class' => 'd-block'],
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'w-100'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => BugReport::class,
       ]);
    }
}
