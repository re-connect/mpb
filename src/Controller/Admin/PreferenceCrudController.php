<?php

namespace App\Controller\Admin;

use App\Entity\Preference;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PreferenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Preference::class;
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
