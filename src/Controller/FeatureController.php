<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Feature;
use App\Form\CommentType;
use App\Form\FeatureType;
use App\Manager\VoteManager;
use App\Repository\FeatureRepository;
use App\Security\Voter\Permissions;
use App\Service\FeatureService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/features')]
class FeatureController extends AbstractController
{
    #[Route('/list', name: 'features_list')]
    public function index(FeatureRepository $repository): Response
    {
        return $this->render('feature/index.html.twig', [
            'features' => $repository->findAll(),
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

        return $this->renderForm('feature/new.html.twig', ['form' => $form]);
    }

    #[Route(path: '/show/{id}', name: 'feature_show', methods: ['GET'])]
    public function show(Feature $feature): Response
    {
        return $this->render('feature/show.html.twig', ['feature' => $feature]);
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

        return $this->renderForm('feature/add_comment.html.twig', [
            'feature' => $feature,
            'form' => $form,
        ]);
    }

    #[IsGranted(Permissions::READ, 'feature')]
    #[Route(path: '/{id}/vote', name: 'feature_vote', methods: ['GET'])]
    public function vote(Feature $feature, VoteManager $manager): Response
    {
        $manager->voteForItem($feature);

        return $this->redirectToRoute('features_list');
    }

    #[IsGranted(Permissions::READ, 'feature')]
    #[Route(path: '/{id}/unvote', name: 'feature_unvote', methods: ['GET'])]
    public function unVote(Feature $feature, VoteManager $manager): Response
    {
        $manager->unVoteForItem($feature);

        return $this->redirectToRoute('features_list');
    }
}
