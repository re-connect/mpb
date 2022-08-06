<?php

namespace App\Controller;

use App\Entity\Bug;
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
#[Route(path: '/bugs')]
class BugReportController extends AbstractController
{
    #[Route(path: '/list', name: 'bug_index', methods: ['GET'])]
    public function index(Request $request, BugReportService $service, ApplicationRepository $applicationRepository): Response
    {
        $showDone = $request->query->getBoolean('done');
        $application = $request->query->getInt('app');

        return $this->render('bug/index.html.twig', [
            'bugs' => $service->getAccessible($showDone, $application),
            'done' => $showDone,
            'applications' => $applicationRepository->findAll(),
        ]);
    }

    #[Route(path: '/create', name: 'bug_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BugReportService $service): Response
    {
        $bug = $service->initBugReport($request->headers->get('User-Agent', ''));
        $form = $this->createForm(BugReportType::class, $bug)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ?UploadedFile $attachment */
            $attachment = $form->get('attachement')->getData();
            $service->create($bug, $attachment);

            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('bug/new.html.twig', ['bug' => $bug, 'form' => $form]);
    }

    #[Route(path: '/{id}/add-screenshot', name: 'add_screenshot', methods: ['GET', 'POST'])]
    public function addScreenshot(Bug $bug): Response
    {
        return $this->render('bug/add_screenshot.html.twig', ['bug' => $bug]);
    }

    #[IsGranted(Permissions::MANAGE, 'bug')]
    #[Route(path: '/{id}', name: 'bug_show', methods: ['GET'])]
    public function show(Bug $bug): Response
    {
        return $this->render('bug/show.html.twig', ['bug' => $bug]);
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id}/edit', name: 'bug_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bug $bug, BugReportService $service): Response
    {
        $form = $this->createForm(BugReportType::class, $bug);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ?UploadedFile $attachment */
            $attachment = $form->get('attachement')->getData();
            $service->update($bug, $attachment);

            return $this->redirectToRoute('bug_index');
        }

        return $this->renderForm('bug/edit.html.twig', ['bug' => $bug, 'form' => $form]);
    }

    #[IsGranted(Permissions::MANAGE, 'bug')]
    #[Route(path: '/{id}', name: 'bug_delete', methods: ['POST'])]
    public function delete(Request $request, Bug $bug, EntityManagerInterface $em): Response
    {
        $csrfTokenName = sprintf('delete%d', $bug->getId());
        if ($this->isCsrfTokenValid($csrfTokenName, (string) $request->request->get('_token', ''))) {
            $em->remove($bug);
            $em->flush();
        }

        return $this->redirectToRoute('bug_index');
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id}/take-over', name: 'bug_take_over', methods: ['GET'])]
    public function takeOver(Bug $bug, EntityManagerInterface $em): Response
    {
        $bug->setAssignee($this->getUser());
        $em->flush();

        return $this->redirectToRoute('bug_index');
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id}/mark-done', name: 'bug_mark_done', methods: ['GET'])]
    public function markDone(Bug $bug, BugReportService $service): Response
    {
        $service->markAsDone($bug);

        return $this->redirectToRoute('bug_index');
    }
}
