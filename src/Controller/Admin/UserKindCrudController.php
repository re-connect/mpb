<?php

namespace App\Controller\Admin;

use App\Entity\UserKind;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserKindCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserKind::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
