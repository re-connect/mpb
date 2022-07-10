<?php

namespace App\Repository;

use App\Entity\BugReport;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BugReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method BugReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method BugReport[]    findAll()
 * @method BugReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<BugReport>
 */
class BugReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BugReport::class);
    }

    /**
     * @return BugReport[]
     */
    public function findByUser(?User $user): array
    {
        /** @var BugReport[] $bugReports */
        $bugReports = $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $bugReports;
    }

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
