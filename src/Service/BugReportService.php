<?php

namespace App\Service;

use App\Entity\BugReport;
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
        private readonly string $uploadsDirectory,
        Security $security,
    ) {
        $this->security = $security;
    }

    public function initBugReport(string $userAgent): BugReport
    {
        return (new BugReport())->setUserAgent($userAgent);
    }

    public function create(BugReport $bug, ?UploadedFile $attachment = null): void
    {
        $bug->setUser($this->getUser());

        if ($attachment) {
            $attachmentName = Uuid::v4().'.'.$attachment->guessExtension();
            $attachment->move($this->uploadsDirectory, $attachmentName);
            $bug->setAttachementName($attachmentName);
        }

        $this->em->persist($bug);
        $this->em->flush();
        $this->notificator->notifyBug($bug);
    }

    /**
     * @return BugReport[]
     */
    public function getAccessible(): array
    {
        $criteria = [];
        $orderBy = ['done' => Criteria::ASC, 'createdAt' => Criteria::DESC];
        if (!$this->authorizationChecker->isGranted('ROLE_TEAM')) {
            $criteria['user'] = $this->getUser();
        }

        return $this->repository->findBy($criteria, $orderBy);
    }

    public function markAsDone(BugReport $bug): void
    {
        $bug->setDone(true);
        $this->em->flush();
        $this->notificator->notifyBug($bug);
    }
}
