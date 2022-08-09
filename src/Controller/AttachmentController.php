<?php

namespace App\Controller;

use App\Entity\Bug;
use App\Security\Voter\Permissions;
use App\Service\BugReportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/attachments')]
class AttachmentController extends AbstractController
{
    #[Route(path: '/bug/{id}', name: 'add_attachment', methods: ['POST'])]
    #[IsGranted(Permissions::MANAGE, 'bug')]
    public function addAttachment(Request $request, Bug $bug, BugReportService $bugService): Response
    {
        $bugService->addAttachment($bug, $request->files->get('file'));

        return $this->json(['OK']);
    }

    #[Route(path: '/bug/{id}/widget', name: 'attachment_widget', methods: ['GET'])]
    #[IsGranted(Permissions::MANAGE, 'bug')]
    public function getAttachmentWidget(Bug $bug): Response
    {
        return $this->render('attachment/_widget.html.twig', ['bug' => $bug]);
    }
}
