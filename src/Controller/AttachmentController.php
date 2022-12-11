<?php

namespace App\Controller;

use App\Entity\Bug;
use App\Security\Voter\Permissions;
use App\Service\BugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/attachments')]
class AttachmentController extends AbstractController
{
    #[Route(path: '/bug/{id}', name: 'add_attachment', methods: ['POST'])]
    #[IsGranted(Permissions::READ, 'bug')]
    public function addAttachment(Request $request, Bug $bug, BugService $bugService): Response
    {
        $bugService->addAttachment($bug, $request->files->get('file'));

        return $this->json(['OK']);
    }

    #[Route(path: '/bug/{id}/widget', name: 'attachment_widget', methods: ['GET'])]
    #[IsGranted(Permissions::READ, 'bug')]
    public function getAttachmentWidget(Bug $bug): Response
    {
        return $this->render('attachment/_widget.html.twig', ['bug' => $bug]);
    }
}
