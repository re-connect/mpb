<?php

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Bug;
use App\Form\Model\Search;
use App\Repository\ApplicationRepository;
use App\Repository\BugRepository;
use App\Traits\UserAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

class BugService
{
    use UserAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BugRepository $repository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly NotificationService $notificator,
        private readonly ApplicationRepository $applicationRepository,
        private readonly string $uploadsDirectory,
        Security $security,
    ) {
        $this->security = $security;
    }

    public function initBug(string $userAgent): Bug
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

    /** @return Bug[] */
    public function getAccessible(Search $search): array
    {
        $parameters = ['done' => $search->getShowDone() ?? false];

        $qb = $this->repository->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')
            ->leftJoin('b.application', 'a')
            ->andWhere('b.done = :done')
            ->addOrderBy('b.done', Criteria::ASC)
            ->addOrderBy('b.createdAt', Criteria::DESC);

        if ($applicationId = $search->getApplication()) {
            $qb->andWhere('a.id = :application');
            $parameters['application'] = $applicationId;
        }
        if (!$this->authorizationChecker->isGranted('ROLE_TEAM')) {
            $qb->andWhere('b.user = :user');
            $parameters['user'] = $this->getUser();
        }
        if ($searchText = $search->getText()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(b.title)', ':searchText'),
                    $qb->expr()->like('LOWER(b.content)', ':searchText'),
                    $qb->expr()->like('LOWER(u.email)', ':searchText'),
                    $qb->expr()->like('LOWER(a.name)', ':searchText'),
                )
            );
            $parameters['searchText'] = '%'.strtolower($searchText).'%';
        }

        /** @var Bug[] $bugs */
        $bugs = $qb->setParameters($parameters)->getQuery()->getResult();

        return $bugs;
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
}
