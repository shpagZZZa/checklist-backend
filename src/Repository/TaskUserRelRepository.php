<?php

namespace App\Repository;

use App\Entity\TaskUserRel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskUserRel|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskUserRel|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskUserRel[]    findAll()
 * @method TaskUserRel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskUserRelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskUserRel::class);
    }

    // /**
    //  * @return TaskUserRel[] Returns an array of TaskUserRel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskUserRel
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
