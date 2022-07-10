<?php

namespace App\Service;

use App\Entity\BugReport;
use App\Repository\StatusRepository;
use App\Traits\UserAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class BugReportService
{
    private EntityManagerInterface $em;
    private StatusRepository $statusRepository;
    use UserAwareTrait;

    public function __construct(EntityManagerInterface $em, StatusRepository $statusRepository, Security $security)
    {
        $this->em = $em;
        $this->statusRepository = $statusRepository;
        $this->security = $security;
    }

    public function initBugReport(Request $request): BugReport
    {
        $bugReport = new BugReport();
        $userAgent = $request->headers->get('User-Agent', '');
        $os = str_contains($userAgent, 'Mac') ? 'Mac' : 'Windows';
        $browser = 'Chrome';
        if (str_contains($userAgent, 'Internet')) {
            $browser = 'Internet Explorer';
        } elseif (str_contains($userAgent, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'Edge')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'Safari')) {
            $browser = 'Safari';
        }
        $deviceId = array_search($os, BugReport::DEVICES);
        $browserId = array_search($browser, BugReport::BROWSERS);
        $bugReport
            ->setDevice(false === $deviceId ? '' : (string) $deviceId)
            ->setBrowser(false === $browserId ? '' : (string) $browserId)
            ->setDeviceLanguage('fr');

        return $bugReport;
    }

    public function create(BugReport $bugReport): void
    {
        $bugReport->setUser($this->getUser())
            ->setCreatedAt(new \DateTime('now'))
            ->setStatus($this->statusRepository->findOneBy(['name' => 'Pas encore pris en compte']));
        $this->em->persist($bugReport);
        $this->em->flush();
    }
}
