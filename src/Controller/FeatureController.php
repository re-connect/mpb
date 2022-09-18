<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Feature;
use App\Form\CommentType;
use App\Form\FeatureType;
use App\Repository\FeatureRepository;
use App\Service\FeatureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/features')]
class FeatureController extends AbstractController
{
    #[Route('/list', name: 'features_list')]
    public function index(FeatureRepository $repository): Response
    {
        $features = $repository->findAll();

        return $this->render('feature/index.html.twig', [
            'features' => $features,
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

        return $this->renderForm('feature/new.html.twig', ['feature' => $feature, 'form' => $form]);
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
}
