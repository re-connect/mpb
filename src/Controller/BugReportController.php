<?php

namespace App\Controller;

use App\Entity\BugReport;
use App\Form\BugReportType;
use App\Repository\ApplicationRepository;
use App\Security\Voter\Permissions;
use App\Service\BugReportService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
#[Route(path: '/bug-report')]
class BugReportController extends AbstractController
{
    #[Route(path: '/list', name: 'bug_report_index', methods: ['GET'])]
    public function index(Request $request, BugReportService $service, ApplicationRepository $applicationRepository): Response
    {
        $showDone = $request->query->getBoolean('done');
        $application = $request->query->getInt('app');

        return $this->render('bug_report/index.html.twig', [
            'bug_reports' => $service->getAccessible($showDone, $application),
            'done' => $showDone,
            'applications' => $applicationRepository->findAll(),
        ]);
    }

    #[Route(path: '/create', name: 'bug_report_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BugReportService $service): Response
    {
        $bugReport = $service->initBugReport($request->headers->get('User-Agent', ''));
        $form = $this->createForm(BugReportType::class, $bugReport)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ?UploadedFile $attachment */
            $attachment = $form->get('attachement')->getData();
            $service->create($bugReport, $attachment);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('bug_report/new.html.twig', [
            'bug_report' => $bugReport,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/add-screenshot', name: 'add_screenshot', methods: ['GET', 'POST'])]
    public function addScreenshot(BugReport $bugReport): Response
    {
        return $this->render('bug_report/add_screenshot.html.twig', [
            'bug' => $bugReport,
        ]);
    }

    #[IsGranted(Permissions::MANAGE, 'bugReport')]
    #[Route(path: '/{id}', name: 'bug_report_show', methods: ['GET'])]
    public function show(BugReport $bugReport): Response
    {
        return $this->render('bug_report/show.html.twig', [
            'bug_report' => $bugReport,
        ]);
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id}/edit', name: 'bug_report_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BugReport $bugReport, BugReportService $service): Response
    {
        $form = $this->createForm(BugReportType::class, $bugReport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ?UploadedFile $attachment */
            $attachment = $form->get('attachement')->getData();
            $service->update($bugReport, $attachment);

            return $this->redirectToRoute('bug_report_index');
        }

        return $this->render('bug_report/edit.html.twig', [
            'bug_report' => $bugReport,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted(Permissions::MANAGE, 'bugReport')]
    #[Route(path: '/{id}', name: 'bug_report_delete', methods: ['POST'])]
    public function delete(Request $request, BugReport $bugReport, EntityManagerInterface $em): Response
    {
        $csrfTokenName = sprintf('delete%d', $bugReport->getId());
        if ($this->isCsrfTokenValid($csrfTokenName, (string) $request->request->get('_token', ''))) {
            $em->remove($bugReport);
            $em->flush();
        }

        return $this->redirectToRoute('bug_report_index');
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id}/take-over', name: 'bug_report_take_over', methods: ['GET'])]
    public function takeOver(BugReport $bugReport, EntityManagerInterface $em): Response
    {
        $bugReport->setAssignee($this->getUser());
        $em->flush();

        return $this->redirectToRoute('bug_report_index');
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id}/mark-done', name: 'bug_report_mark_done', methods: ['GET'])]
    public function markDone(BugReport $bugReport, BugReportService $service): Response
    {
        $service->markAsDone($bugReport);

        return $this->redirectToRoute('bug_report_index');
    }
}
