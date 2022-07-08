<?php

namespace App\Controller;

use App\Entity\BugReport;
use App\Form\BugReportType;
use App\Repository\BugReportRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route(path: '/bug-report')]
class BugReportController extends AbstractController
{
    #[Route(path: '/list', name: 'bug_report_index', methods: ['GET'])]
    public function index(BugReportRepository $bugReportRepository): Response
    {
        return $this->render('bug_report/index.html.twig', [
            'bug_reports' => $bugReportRepository->findAll(),
        ]);
    }

    #[Route(path: '/create', name: 'bug_report_new', methods: ['GET', 'POST'])]
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
            ->setBrowser(array_search($browser, BugReport::BROWSERS))
            ->setDeviceLanguage('fr');
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

    #[Route(path: '/{id}', name: 'bug_report_show', methods: ['GET'])]
    public function show(BugReport $bugReport): Response
    {
        return $this->render('bug_report/show.html.twig', [
            'bug_report' => $bugReport,
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'bug_report_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BugReport $bugReport, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BugReportType::class, $bugReport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('bug_report_index');
        }

        return $this->render('bug_report/edit.html.twig', [
            'bug_report' => $bugReport,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'bug_report_delete', methods: ['POST'])]
    public function delete(Request $request, BugReport $bugReport, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bugReport->getId(), $request->request->get('_token'))) {
            $em->remove($bugReport);
            $em->flush();
        }

        return $this->redirectToRoute('bug_report_index');
    }
}
