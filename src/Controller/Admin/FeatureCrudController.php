<?php

namespace App\Controller\Admin;

use App\Entity\Feature;
use App\Repository\FeatureRepository;
use App\Service\AdminExportService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeatureCrudController extends AbstractCrudController
{
    public function __construct(private readonly FeatureRepository $repository, private readonly AdminExportService $exportService)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Feature::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('application', 'application'),
            TextField::new('title', 'title'),
            TextareaField::new('content', 'description'),
            TextField::new('user', 'user'),
            TextField::new('status.value', 'status'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $export = Action::new('export', 'action_export')
            ->setIcon('fa fa-download')
            ->linkToCrudAction('export')
            ->setCssClass('btn')
            ->createAsGlobalAction();

        return $actions->add(Crud::PAGE_INDEX, $export);
    }

    public function export(): StreamedResponse
    {
        $fields = Feature::EXPORTABLE_FIELDS;
        $data = array_map(fn (Feature $feature) => $feature->getExportableData(), $this->repository->findAll());

        return $this->exportService->export($data, $fields);
    }
}
