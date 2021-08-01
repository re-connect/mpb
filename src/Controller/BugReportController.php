<?php

namespace App\Controller;

use App\Entity\BugReport;
use App\Form\BugReportType;
use App\Repository\BugReportRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function get_browser;
use function str_contains;

/**
 * @Route("/bug-report")
 * @IsGranted ("ROLE_USER")
 */
class BugReportController extends AbstractController
{
    /**
     * @Route("/list", name="bug_report_index", methods={"GET"})
     */
    public function index(BugReportRepository $bugReportRepository): Response
    {
        return $this->render('bug_report/index.html.twig', [
            'bug_reports' => $bugReportRepository->findAll(),
        ]);
    }

    /**
     * @Route("/create", name="bug_report_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, StatusRepository $statusRepository): Response
    {
        $bugReport = new BugReport();

        $userAgent = $request->headers->get('User-Agent');
        $os = str_contains($userAgent, 'Mac') ? 'Mac' : 'Windows';
        $browser = 'Chrome';
        if (str_contains($userAgent, 'Internet')) {
            $browser = 'Internet Explorer';
        } elseif (str_contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Edge')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'Safari')) {
            $browser = 'Safari';
        }
        $bugReport
            ->setDevice(array_search($os, BugReport::DEVICES))
            ->setBrowser(array_search($browser, BugReport::BROWSERS));

        $form = $this->createForm(BugReportType::class, $bugReport, [
            'userAgent' => $userAgent,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bugReport->setUser($this->getUser())
            ->setCreatedAt(new \DateTime('now'))
            ->setStatus($statusRepository->findOneBy(['name' => 'Pas encore pris en compte']));
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
