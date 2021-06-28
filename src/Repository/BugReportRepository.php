<?php

namespace App\Repository;

use App\Entity\BugReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BugReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method BugReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method BugReport[]    findAll()
 * @method BugReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BugReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BugReport::class);
    }

    // /**
    //  * @return BugReport[] Returns an array of BugReport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BugReport
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
