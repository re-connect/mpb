<?php

namespace App\Controller\Admin;

use App\Entity\Attachment;
use App\Entity\Badge;
use App\Entity\BugReport;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Preference;
use App\Entity\Status;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Mpb');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
         yield MenuItem::linkToCrud('BugReport', 'fas fa-bug', BugReport::class);
         yield MenuItem::linkToCrud('User', 'fas fa-users', User::class);
//         yield MenuItem::linkToCrud('Attachment', 'fas fa-list', Attachment::class);
//         yield MenuItem::linkToCrud('Badge', 'fas fa-list', Badge::class);
//         yield MenuItem::linkToCrud('Category', 'fas fa-list', Category::class);
//         yield MenuItem::linkToCrud('Comment', 'fas fa-list', Comment::class);
//         yield MenuItem::linkToCrud('Preference', 'fas fa-list', Preference::class);
//         yield MenuItem::linkToCrud('Status', 'fas fa-list', Status::class);
    }
}
