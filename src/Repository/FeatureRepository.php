<?php

namespace App\Repository;

use App\Entity\Feature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feature>
 *
 * @method Feature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feature[]    findAll()
 * @method Feature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeatureRepository extends ServiceEntityRepository implements UserRequestRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feature::class);
    }

    public function add(Feature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Feature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return mixed[] */
    public function getAllCenters(): array
    {
        return $this->createQueryBuilder('f')
            ->select('f.center')
            ->where('f.center IS NOT NULL')
            ->getQuery()->getArrayResult();
    }

    /**
     * @return Feature[]
     */
    public function findDraftsToClean(): array
    {
        $qb = $this->createQueryBuilder("f");

        return $qb->where("f.draft = :draft")
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull("f.title"),
                    $qb->expr()->eq("f.title", "''")
                )
            )
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull("f.content"),
                    $qb->expr()->eq("f.content", "''")
                )
            )
            ->setParameter("draft", true)
            ->getQuery()
            ->getResult();
    }
}
