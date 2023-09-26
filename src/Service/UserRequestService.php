<?php

namespace App\Service;

use App\Entity\UserRequest;
use App\Form\Model\UserRequestSearch;
use App\Repository\UserRequestRepositoryInterface;
use App\Traits\UserAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserRequestService
{
    use UserAwareTrait;

    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly Security $security,
    ) {
    }

    /** @return UserRequest[] */
    public function getAccessibleUserRequests(UserRequestSearch $search, UserRequestRepositoryInterface $repository): array
    {
        $parameters = ['done' => $search->getShowDone() ?? false];

        /* @phpstan-ignore-next-line */
        $qb = $repository->createQueryBuilder('ur')
            ->leftJoin('ur.user', 'u')
            ->leftJoin('ur.application', 'a')
            ->andWhere('ur.done = :done')
            ->addOrderBy('ur.done', Criteria::ASC)
            ->addOrderBy('ur.createdAt', Criteria::DESC);

        if ($applicationId = $search->getApplication()) {
            $qb->andWhere('a.id = :application');
            $parameters['application'] = $applicationId;
        }
        if (!$this->authorizationChecker->isGranted('ROLE_TEAM')) {
            $qb->andWhere('ur.user = :user');
            $parameters['user'] = $this->getUser();
        }
        if ($searchText = $search->getText()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(ur.title)', ':searchText'),
                    $qb->expr()->like('LOWER(ur.content)', ':searchText'),
                    $qb->expr()->like('LOWER(u.email)', ':searchText'),
                    $qb->expr()->like('LOWER(a.name)', ':searchText'),
                )
            );
            $parameters['searchText'] = '%'.strtolower($searchText).'%';
        }

        return $qb->setParameters($parameters)->getQuery()->getResult();
    }
}
