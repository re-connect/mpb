<?php

namespace App\Controller\Admin;

use App\Entity\Vote;
use App\Repository\VoteRepository;
use App\Service\AdminExportService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VoteCrudController extends AbstractCrudController
{
    public function __construct(private readonly VoteRepository $repository, private readonly AdminExportService $exportService)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Vote::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('bug')->onlyOnIndex(),
            AssociationField::new('feature')->onlyOnIndex(),
            AssociationField::new('voter')->onlyOnIndex(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW, Action::DELETE, Action::EDIT);

        $export = Action::new('export', 'action_export')
            ->setIcon('fa fa-download')
            ->linkToCrudAction('export')
            ->setCssClass('btn')
            ->createAsGlobalAction();

        return $actions->add(Crud::PAGE_INDEX, $export);
    }

    public function export(): StreamedResponse
    {
        $data = array_map(fn (Vote $vote) => $vote->getExportableData(), $this->repository->findAll());

        return $this->exportService->export($data, Vote::EXPORTABLE_FIELDS);
    }
}
