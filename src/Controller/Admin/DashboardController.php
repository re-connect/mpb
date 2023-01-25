<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use App\Entity\Bug;
use App\Entity\Feature;
use App\Entity\Tag;
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
        return Dashboard::new()->setTitle('MPB');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('bug', 'fas fa-bug', Bug::class);
        yield MenuItem::linkToCrud('features', 'fas fa-star', Feature::class);
        yield MenuItem::linkToCrud('user', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('application', 'fas fa-computer', Application::class);
        yield MenuItem::linkToCrud('user_kind', 'fas fa-user', UserKind::class);
        yield MenuItem::linkToCrud('tags', 'fas fa-tag', Tag::class);
    }
}
