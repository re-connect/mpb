<?php

namespace App\Controller\Admin;

use App\Entity\Requester;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RequesterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Requester::class;
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
