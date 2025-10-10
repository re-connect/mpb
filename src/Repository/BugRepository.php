<?php

namespace App\Repository;

use App\Entity\Bug;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Bug|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bug|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bug[]    findAll()
 * @method Bug[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Bug>
 */
class BugRepository extends ServiceEntityRepository implements UserRequestRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bug::class);
    }

    /**
     * @return Bug[]
     */
    public function findDraftsToClean(): array
    {
        $qb = $this->createQueryBuilder('b');

        return $qb->where('b.draft = :draft')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('b.title'),
                    $qb->expr()->eq('b.title', "''")
                )
            )
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull('b.content'),
                    $qb->expr()->eq('b.content', "''")
                )
            )
            ->setParameter('draft', true)
            ->getQuery()
            ->getResult();
    }
}
