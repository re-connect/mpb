<?php

namespace App\Controller\Admin;

use App\Entity\Center;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CenterCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud
            ->setEntityLabelInPlural('centers')
            ->setEntityLabelInSingular('center')
        );
    }

    public static function getEntityFqcn(): string
    {
        return Center::class;
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
