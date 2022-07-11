<?php

namespace App\Service;

use App\Entity\BugReport;
use App\Repository\BugReportRepository;
use App\Repository\StatusRepository;
use App\Traits\UserAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class BugReportService
{
    use UserAwareTrait;

    public function __construct(private readonly EntityManagerInterface $em, private readonly BugReportRepository $repository, private readonly StatusRepository $statusRepository, Security $security, private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->security = $security;
    }

    public function initBugReport(string $userAgent): BugReport
    {
        return (new BugReport())->setUserAgent($userAgent);
    }

    public function create(BugReport $bugReport): void
    {
        $bugReport
            ->setUser($this->getUser())
            ->setCreatedAt(new \DateTime('now'))
            ->setStatus($this->statusRepository->findOneBy(['name' => 'Pas encore pris en compte']));
        $this->em->persist($bugReport);
        $this->em->flush();
    }

    /**
     * @return BugReport[]
     */
    public function getAccessible(): array
    {
        return $this->authorizationChecker->isGranted('ROLE_TEAM')
            ? $this->repository->findAll()
            : $this->repository->findByUser($this->getUser());
    }
}
