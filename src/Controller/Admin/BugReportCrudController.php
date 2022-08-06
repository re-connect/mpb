<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use App\Entity\Bug;
use App\Entity\User;
use App\Entity\UserKind;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BugReportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bug::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextEditorField::new('content'),
            AssociationField::new('application')->setCrudController(Application::class),
            AssociationField::new('user')->setCrudController(User::class),
            AssociationField::new('assignee')->setCrudController(User::class),
            AssociationField::new('userKind')->setCrudController(UserKind::class),
            TextField::new('url'),
            IntegerField::new('accountId'),
            IntegerField::new('itemId'),
            TextField::new('userAgent'),
            DateTimeField::new('createdAt'),
        ];
    }
}
