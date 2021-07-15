<?php

namespace App\Controller;

use App\Entity\BugReport;
use App\Form\BugReportType;
use App\Repository\BugReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bug/report")
 */
class BugReportController extends AbstractController
{
    /**
     * @Route("/", name="bug_report_index", methods={"GET"})
     */
    public function index(BugReportRepository $bugReportRepository): Response
    {
        return $this->render('bug_report/index.html.twig', [
            'bug_reports' => $bugReportRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="bug_report_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $bugReport = new BugReport();
        $form = $this->createForm(BugReportType::class, $bugReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bugReport->setUser($this->getUser())
            ->setCreatedAt(new \DateTime('now'));
            $em->persist($bugReport);
            $em->flush();

            return $this->redirectToRoute('bug_report_index');
        }

        return $this->render('bug_report/new.html.twig', [
            'bug_report' => $bugReport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bug_report_show", methods={"GET"})
     */
    public function show(BugReport $bugReport): Response
    {
        return $this->render('bug_report/show.html.twig', [
            'bug_report' => $bugReport,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bug_report_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, BugReport $bugReport): Response
    {
        $form = $this->createForm(BugReportType::class, $bugReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bug_report_index');
        }

        return $this->render('bug_report/edit.html.twig', [
            'bug_report' => $bugReport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bug_report_delete", methods={"POST"})
     */
    public function delete(Request $request, BugReport $bugReport): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bugReport->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($bugReport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('bug_report_index');
    }
}
