<?php

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Bug;
use App\Repository\ApplicationRepository;
use App\Repository\BugReportRepository;
use App\Traits\UserAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

class BugReportService
{
    use UserAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BugReportRepository $repository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly NotificationService $notificator,
        private readonly ApplicationRepository $applicationRepository,
        private readonly string $uploadsDirectory,
        Security $security,
    ) {
        $this->security = $security;
    }

    public function initBugReport(string $userAgent): Bug
    {
        $bug = (new Bug())->setUserAgent($userAgent);
        $this->em->persist($bug);
        $this->em->flush();

        return $bug;
    }

    public function create(Bug $bug): void
    {
        $bug->publish();
        $this->em->flush();
        $this->notificator->notifyBug($bug);
    }

    /**
     * @return Bug[]
     */
    public function getAccessible(bool $done = false, int $applicationId = 0): array
    {
        $criteria = ['done' => $done];
        if ($applicationId) {
            $criteria['application'] = $this->applicationRepository->find($applicationId);
        }
        $orderBy = ['done' => Criteria::ASC, 'createdAt' => Criteria::DESC];
        if (!$this->authorizationChecker->isGranted('ROLE_TEAM')) {
            $criteria['user'] = $this->getUser();
        }

        return $this->repository->findBy($criteria, $orderBy);
    }

    public function markAsDone(Bug $bug): void
    {
        $bug->setDone(true);
        $this->em->flush();
        $this->notificator->notifyBug($bug);
    }

    public function addAttachment(Bug $bug, mixed $file): void
    {
        if (!($file instanceof UploadedFile)) {
            return;
        }

        $name = Uuid::v4().'.'.$file->guessExtension();
        $attachment = (new Attachment())
            ->setBug($bug)
            ->setName($name)
            ->setSize($file->getSize())
            ->setUploadedBy($this->getUser());
        $this->em->persist($attachment);
        $this->em->flush();
        $bug->addAttachment($attachment);

        $file->move($this->uploadsDirectory, $name);
    }

    public function handleAttachment(Bug $bug, ?UploadedFile $attachment): void
    {
        if (!$attachment) {
            return;
        }

        $attachmentName = Uuid::v4().'.'.$attachment->guessExtension();
        $attachment->move($this->uploadsDirectory, $attachmentName);
        $bug->setAttachementName($attachmentName);
    }
}
