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
use Symfony\UX\Dropzone\Form\DropzoneType;

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
            ->add('application', StyledEntityType::class, [
                'required' => true,
                'class' => Application::class,
                'placeholder' => 'Application',
            ])
            ->add('userKind', StyledEntityType::class, [
                'class' => UserKind::class,
                'placeholder' => "Type d'utilisateur",
            ])
            ->add('url', TextType::class, [
                'label' => 'URL (exemple : https://pro.reconnect.fr/families)',
                'attr' => [
                    'placeholder' => 'URL (exemple : https://pro.reconnect.fr/families)',
                ],
                'required' => false,
            ])
            ->add('accountId', IntegerType::class, [
                'label' => 'ID compte',
                'required' => false,
                'attr' => [
                    'placeholder' => 'ID compte',
                ],
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Description du bug rencontré',
            ])
            ->add('attachement', DropzoneType::class, [
                'label' => 'Ajouter un screenshot',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Drag and drop a file or click to browse',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bug::class,
        ]);
    }
}
