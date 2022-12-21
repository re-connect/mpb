<?php

namespace App\Service;

use App\Entity\Feature;
use App\Form\Model\Search;
use App\Repository\FeatureRepository;
use App\Traits\UserAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class FeatureService
{
    use UserAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FeatureRepository $repository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly Security $security,
    ) {
    }

    public function create(Feature $feature): void
    {
        $this->em->persist($feature);
        $this->em->flush();
    }

    /** @return Feature[] */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /** @return Feature[] */
    public function getAccessible(Search $search): array
    {
        $parameters = ['done' => $search->getShowDone() ?? false];

        $qb = $this->repository->createQueryBuilder('f')
            ->leftJoin('f.user', 'u')
            ->leftJoin('f.application', 'a')
            ->andWhere('f.done = :done')
            ->addOrderBy('f.done', Criteria::ASC)
            ->addOrderBy('f.createdAt', Criteria::DESC);

        if ($applicationId = $search->getApplication()) {
            $qb->andWhere('a.id = :application');
            $parameters['application'] = $applicationId;
        }
        if (!$this->authorizationChecker->isGranted('ROLE_TEAM')) {
            $qb->andWhere('f.user = :user');
            $parameters['user'] = $this->getUser();
        }
        if ($searchText = $search->getText()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(f.title)', ':searchText'),
                    $qb->expr()->like('LOWER(f.content)', ':searchText'),
                    $qb->expr()->like('LOWER(u.email)', ':searchText'),
                    $qb->expr()->like('LOWER(a.name)', ':searchText'),
                )
            );
            $parameters['searchText'] = '%'.strtolower($searchText).'%';
        }

        /** @var Feature[] $features */
        $features = $qb->setParameters($parameters)->getQuery()->getResult();

        return $features;
    }

    /**
     * @return array<array<string, string>>
     */
    public function getAllCentersForAutocomplete(): array
    {
        $centers = array_unique(array_column($this->repository->getAllCentersForAutocomplete(), 'center'));

        return array_map(fn (string $center) => ['value' => $center, 'text' => $center], $centers);
    }
}
