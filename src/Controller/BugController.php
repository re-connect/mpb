<?php

namespace App\Controller;

use App\Entity\Bug;
use App\Entity\Comment;
use App\Form\BugType;
use App\Form\CommentType;
use App\Form\Model\UserRequestSearch;
use App\Form\SearchType;
use App\Manager\BugManager;
use App\Manager\CommentManager;
use App\Manager\VoteManager;
use App\Repository\ApplicationRepository;
use App\Security\Voter\Permissions;
use App\Service\BugService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route(path: '/bugs')]
class BugController extends AbstractController
{
    #[Route(path: '/list', name: 'bugs_list', methods: ['GET'])]
    public function index(Request $request, BugService $service, ApplicationRepository $applicationRepository): Response
    {
        $search = new UserRequestSearch(null, $request->query->getBoolean('done'), $request->query->getInt('app'));
        $form = $this->createForm(SearchType::class, null, [
            'action' => $this->generateUrl('bug_search', $request->query->all()),
        ]);

        return $this->render('bug/index.html.twig', [
            'bugs' => $service->getAccessible($search),
            'done' => $search->getShowDone(),
            'form' => $form,
            'applications' => $applicationRepository->findAll(),
        ]);
    }

    #[Route(path: '/search', name: 'bug_search', methods: ['POST'])]
    public function search(Request $request, BugService $service): Response
    {
        $search = new UserRequestSearch(null, $request->query->getBoolean('done'), $request->query->getInt('app'));
        $this->createForm(SearchType::class, $search)->handleRequest($request);

        return $this->render('bug/_list.html.twig', [
            'bugs' => $service->getAccessible($search),
        ]);
    }

    #[Route(path: '/init', name: 'bug_init', methods: ['GET', 'POST'])]
    public function init(Request $request, BugManager $manager): Response
    {
        $bug = $manager->createBug($request->headers->get('User-Agent', ''));

        return $this->redirectToRoute('bug_new', ['id' => $bug->getId()]);
    }

    #[IsGranted(Permissions::UPDATE, 'bug')]
    #[Route(path: '/create/{id<\d+>}', name: 'bug_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Bug $bug, BugManager $manager): Response
    {
        $form = $this->createForm(BugType::class, $bug)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->publishDraft($bug);

            return $this->redirectToRoute('bugs_list');
        }

        return $this->render('bug/new.html.twig', ['bug' => $bug, 'form' => $form]);
    }

    #[Route(path: '/{id<\d+>}/add-screenshot', name: 'add_screenshot', methods: ['GET', 'POST'])]
    public function addScreenshot(Bug $bug): Response
    {
        return $this->render('bug/add_screenshot.html.twig', ['bug' => $bug]);
    }

    #[IsGranted(Permissions::READ, 'bug')]
    #[Route(path: '/{id<\d+>}', name: 'bug_show', methods: ['GET'])]
    public function show(Bug $bug): Response
    {
        return $this->render('bug/show.html.twig', ['bug' => $bug]);
    }

    #[IsGranted(Permissions::UPDATE, 'bug')]
    #[Route(path: '/{id<\d+>}/edit', name: 'bug_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bug $bug, BugManager $manager): Response
    {
        $form = $this->createForm(BugType::class, $bug)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->publishDraft($bug);

            return $this->redirectToRoute('bugs_list');
        }

        return $this->render('bug/edit.html.twig', ['bug' => $bug, 'form' => $form]);
    }

    #[IsGranted(Permissions::DELETE, 'bug')]
    #[Route(path: '/delete/{id<\d+>}', name: 'bug_delete', methods: ['POST'])]
    public function delete(Request $request, Bug $bug, BugManager $manager): Response
    {
        $csrfTokenName = sprintf('delete%d', $bug->getId());
        if ($this->isCsrfTokenValid($csrfTokenName, (string) $request->request->get('_token', ''))) {
            $manager->remove($bug);
        }

        return $this->redirectToRoute('bugs_list');
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id<\d+>}/take-over', name: 'bug_take_over', methods: ['GET'])]
    public function takeOver(Bug $bug, BugManager $manager): Response
    {
        $manager->takeOver($bug);

        return $this->refreshOrRedirect('bugs_list');
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id<\d+>}/dismiss', name: 'bug_dismiss', methods: ['GET'])]
    public function dismiss(Bug $bug, BugManager $manager): Response
    {
        $manager->dismiss($bug);

        return $this->refreshOrRedirect('bugs_list');
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id<\d+>}/unprioritize', name: 'bug_unprioritize', methods: ['GET'])]
    public function lowerPriority(Bug $bug, BugManager $manager): Response
    {
        $manager->unprioritize($bug);

        return $this->refreshOrRedirect('bugs_list');
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id<\d+>}/solve', name: 'bug_solve', methods: ['GET'])]
    public function solve(Bug $bug, BugManager $manager): Response
    {
        $manager->solve($bug);

        return $this->refreshOrRedirect('bugs_list');
    }

    #[IsGranted(Permissions::READ, 'bug')]
    #[Route(path: '/{id<\d+>}/add-comment', name: 'bug_add_comment', methods: ['GET', 'POST'])]
    public function addComment(Request $request, Bug $bug, CommentManager $manager): Response
    {
        $comment = (new Comment());
        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->create($comment->setBug($bug));

            return $this->redirectToRoute('bug_add_comment', ['id' => $bug->getId()]);
        }

        return $this->render('bug/add_comment.html.twig', ['bug' => $bug, 'form' => $form]);
    }

    #[Route(path: '/{id<\d+>}/vote', name: 'bug_vote', methods: ['GET'])]
    public function vote(Bug $bug, VoteManager $manager): Response
    {
        $manager->vote($bug);

        return $this->refreshOrRedirect('bugs_list');
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id<\d+>}/convert-to-feature', name: 'bug_convert_to_feature', methods: ['GET'])]
    public function convertToFeature(Bug $bug, BugManager $manager): Response
    {
        $feature = $manager->convertToFeature($bug);
        $this->addFlash('success', 'bug_converted_to_feature');

        return $this->redirectToRoute('feature_show', ['id' => $feature->getId()]);
    }
}
