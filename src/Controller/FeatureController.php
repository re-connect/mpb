<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Feature;
use App\Entity\Tag;
use App\Form\CommentType;
use App\Form\FeatureStatusType;
use App\Form\FeatureType;
use App\Form\Model\Search;
use App\Form\SearchType;
use App\Manager\CommentManager;
use App\Manager\FeatureManager;
use App\Manager\VoteManager;
use App\Repository\ApplicationRepository;
use App\Repository\TagRepository;
use App\Security\Voter\Permissions;
use App\Service\FeatureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/features')]
class FeatureController extends AbstractController
{
    #[Route('/list', name: 'features_list')]
    public function index(Request $request, FeatureService $service, ApplicationRepository $applicationRepository): Response
    {
        $search = new Search(null, $request->query->getBoolean('done'), $request->query->getInt('app'));
        $form = $this->createForm(SearchType::class, null, [
            'action' => $this->generateUrl('feature_search', $request->query->all()),
        ]);

        return $this->render('feature/index.html.twig', [
            'features' => $service->getAccessible($search),
            'done' => $search->getShowDone(),
            'form' => $form,
            'applications' => $applicationRepository->findAll(),
        ]);
    }

    #[Route(path: '/search', name: 'feature_search', methods: ['POST'])]
    public function search(Request $request, FeatureService $service): Response
    {
        $search = new Search(null, $request->query->getBoolean('done'), $request->query->getInt('app'));
        $this->createForm(SearchType::class, $search)->handleRequest($request);

        return $this->render('feature/components/_list.html.twig', [
            'features' => $service->getAccessible($search),
        ]);
    }

    #[Route(path: '/init', name: 'feature_init', methods: ['GET', 'POST'])]
    public function init(FeatureManager $manager): Response
    {
        $feature = $manager->createFeature();

        return $this->redirectToRoute('feature_new', ['id' => $feature->getId()]);
    }

    #[IsGranted(Permissions::UPDATE, 'feature')]
    #[Route(path: '/create/{id}', name: 'feature_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Feature $feature, FeatureService $service, FeatureManager $manager): Response
    {
        $form = $this->createForm(FeatureType::class, $feature, ['centerValues' => $service->getAllCentersForAutocomplete()])->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->publishDraft($feature);

            return $this->redirectToRoute('features_list');
        }

        return $this->render('feature/new.html.twig', [
            'form' => $form,
            'feature' => $feature,
        ]);
    }

    #[IsGranted(Permissions::READ, 'feature')]
    #[Route(path: '/{id}', name: 'feature_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Feature $feature, TagRepository $tagRepository, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FeatureStatusType::class, $feature)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($feature);
            $em->flush();

            $this->addFlash('success', 'status_edit_success');

            return $this->redirectToRoute('feature_show', ['id' => $feature->getId()]);
        }

        return $this->render('feature/show.html.twig', [
            'form' => $form,
            'feature' => $feature,
            'tags' => $tagRepository->findAll(),
        ]);
    }

    #[IsGranted(Permissions::UPDATE, 'feature')]
    #[Route(path: '/{id}/edit', name: 'feature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feature $feature, FeatureManager $manager): Response
    {
        $form = $this->createForm(FeatureType::class, $feature)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->publishDraft($feature);

            return $this->redirectToRoute('features_list');
        }

        return $this->render('feature/edit.html.twig', ['feature' => $feature, 'form' => $form]);
    }

    #[IsGranted(Permissions::DELETE, 'feature')]
    #[Route(path: '/delete/{id}', name: 'feature_delete', methods: ['POST'])]
    public function delete(Request $request, Feature $feature, FeatureManager $manager): Response
    {
        $csrfTokenName = sprintf('delete%d', $feature->getId());
        if ($this->isCsrfTokenValid($csrfTokenName, $request->request->getAlpha('_token'))) {
            $manager->remove($feature);
        }

        return $this->redirectToRoute('features_list');
    }

    #[IsGranted(Permissions::READ, 'feature')]
    #[Route(path: '/{id}/add-comment', name: 'feature_add_comment', methods: ['GET', 'POST'])]
    public function addComment(Request $request, Feature $feature, CommentManager $manager): Response
    {
        $comment = (new Comment());
        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->create($comment->setFeature($feature));

            return $this->redirectToRoute('feature_add_comment', ['id' => $feature->getId()]);
        }

        return $this->render('feature/add_comment.html.twig', [
            'feature' => $feature,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id}/tag/{tag}', name: 'feature_tag', methods: ['GET'])]
    public function addTag(Feature $feature, Tag $tag, FeatureManager $manager): Response
    {
        $manager->toggleTag($feature, $tag);

        return $this->redirectToRoute('feature_show', ['id' => $feature->getId()]);
    }

    #[Route(path: '/{id}/vote', name: 'feature_vote', methods: ['GET'])]
    public function vote(Feature $feature, VoteManager $manager): Response
    {
        $manager->voteForItem($feature);

        return $this->refreshOrRedirect('features_list');
    }

    #[Route(path: '/{id}/mark-done', name: 'feature_mark_done', methods: ['GET'])]
    public function markDone(Feature $feature, FeatureManager $manager): Response
    {
        $manager->markDone($feature);

        return $this->refreshOrRedirect('features_list');
    }
}
