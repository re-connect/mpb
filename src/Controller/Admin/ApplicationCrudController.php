<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ApplicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Application::class;
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
