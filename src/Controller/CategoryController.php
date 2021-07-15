<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(EntityManagerInterface $em): RedirectResponse
    {
        $cat1 = (new Category())
            ->setName('Critique');
        $cat2 = (new Category())
            ->setName('Bloquant');
        $cat3 = (new Category())
            ->setName('Minime');
        $em->persist($cat1);
        $em->persist($cat2);
        $em->persist($cat3);
        $em->flush();

        return $this->redirectToRoute('bug_report_index');
    }
}