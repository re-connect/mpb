<?php

namespace App\Repository;

use App\Entity\Requester;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Requester>
 *
 * @method Requester|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requester|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requester[]    findAll()
 * @method Requester[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequesterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requester::class);
    }

    //    /**
    //     * @return Requester[] Returns an array of Requester objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Requester
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
