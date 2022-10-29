<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Feature;
use App\Entity\Tag;
use App\Form\CommentType;
use App\Form\FeatureType;
use App\Form\Model\Search;
use App\Form\SearchType;
use App\Manager\VoteManager;
use App\Repository\ApplicationRepository;
use App\Repository\TagRepository;
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
        $searchForm = $this->createForm(SearchType::class, null, [
            'action' => $this->generateUrl('feature_search', $request->query->all()),
        ]);

        return $this->render('feature/index.html.twig', [
            'features' => $service->getAccessible($search),
            'done' => $search->getShowDone(),
            'searchForm' => $searchForm,
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

    #[Route(path: '/create', name: 'feature_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FeatureService $service): Response
    {
        $feature = new Feature();
        $form = $this->createForm(FeatureType::class, $feature)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->create($feature);

            return $this->redirectToRoute('features_list');
        }

        return $this->render('feature/new.html.twig', ['form' => $form]);
    }

    #[Route(path: '/show/{id}', name: 'feature_show', methods: ['GET'])]
    public function show(Feature $feature, TagRepository $tagRepository): Response
    {
        return $this->render('feature/show.html.twig', [
            'feature' => $feature,
            'tags' => $tagRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_TECH_TEAM')]
    #[Route(path: '/{id}/tag/{tag}', name: 'feature_tag', methods: ['GET'])]
    public function addTag(Feature $feature, Tag $tag, EntityManagerInterface $em): Response
    {
        $feature->toggleTag($tag);
        $em->flush();

        return $this->redirectToRoute('feature_show', ['id' => $feature->getId()]);
    }

    #[Route(path: '/{id}/add-comment', name: 'feature_add_comment', methods: ['GET', 'POST'])]
    public function addComment(Request $request, Feature $feature, EntityManagerInterface $em): Response
    {
        $comment = (new Comment())->setFeature($feature);
        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();
        }

        return $this->render('feature/add_comment.html.twig', [
            'feature' => $feature,
            'form' => $form,
        ]);
    }

    #[Route(path: '/{id}/vote', name: 'feature_vote', methods: ['GET'])]
    public function vote(Feature $feature, VoteManager $manager): Response
    {
        $manager->voteForItem($feature);

        return $this->redirectToRoute('features_list');
    }
}
