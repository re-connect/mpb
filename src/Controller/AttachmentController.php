<?php

namespace App\Controller;

use App\Entity\Bug;
use App\Entity\Feature;
use App\Entity\UserRequest;
use App\Security\Voter\Permissions;
use App\Service\AttachmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/attachments')]
class AttachmentController extends AbstractController
{
    #[Route(path: '/bug/{id}', name: 'bug_add_attachment', methods: ['POST'])]
    #[IsGranted(Permissions::READ, 'bug')]
    public function addBugAttachment(Request $request, Bug $bug, AttachmentService $service): Response
    {
        return $this->addAttachment($bug, $request->files->get('file'), $service);
    }

    #[Route(path: '/bug/{id}/widget', name: 'bug_attachment_widget', methods: ['GET'])]
    #[IsGranted(Permissions::READ, 'bug')]
    public function getBugAttachmentWidget(Bug $bug): Response
    {
        return $this->render('bug/_attachment_widget.html.twig', [
            'bug' => $bug,
        ]);
    }

    #[Route(path: '/feature/{id}', name: 'feature_add_attachment', methods: ['POST'])]
    #[IsGranted(Permissions::READ, 'feature')]
    public function addFeatureAttachment(Request $request, Feature $feature, AttachmentService $service): Response
    {
        return $this->addAttachment($feature, $request->files->get('file'), $service);
    }

    #[Route(path: '/feature/{id}/widget', name: 'feature_attachment_widget', methods: ['GET'])]
    #[IsGranted(Permissions::READ, 'feature')]
    public function getFeatureAttachmentWidget(Feature $feature): Response
    {
        return $this->render('feature/components/_attachment_widget.html.twig', [
            'feature' => $feature,
        ]);
    }

    private function addAttachment(UserRequest $userRequest, mixed $file, AttachmentService $service): Response
    {
        if ($service->addAttachment($userRequest, $file)) {
            return $this->json('OK');
        }

        return $this->json($userRequest, 422);
    }
}
