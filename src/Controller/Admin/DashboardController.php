<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use App\Entity\BugReport;
use App\Entity\User;
use App\Entity\UserKind;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private readonly AdminUrlGenerator $urlGenerator)
    {
    }

    #[Route(path: '/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirect($this->urlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Mpb');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('BugReport', 'fas fa-bug', BugReport::class);
        yield MenuItem::linkToCrud('User', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Application', 'fas fa-computer', Application::class);
        yield MenuItem::linkToCrud('UserKind', 'fas fa-user', UserKind::class);
//         yield MenuItem::linkToCrud('Comment', 'fas fa-list', Comment::class);
    }
}
