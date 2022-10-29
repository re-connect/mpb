<?php

namespace App\Controller\Admin;

use App\Entity\Badge;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BadgeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Badge::class;
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
