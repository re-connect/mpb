<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByRole(string $role): ?User
    {
        $rsm = $this->createResultSetMappingBuilder('u');

        $rawQuery = sprintf('SELECT %s FROM users u WHERE  roles::jsonb ?? :role', $rsm->generateSelectClause());

        $query = $this->getEntityManager()->createNativeQuery($rawQuery, $rsm);
        $query->setParameter('role', $role);
        /** @var User[] $result */
        $result = $query->getResult();

        return $result[0] ?? null;
    }
}
