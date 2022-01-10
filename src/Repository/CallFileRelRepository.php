<?php

namespace App\Repository;

use App\Entity\CallFileRel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CallFileRel|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallFileRel|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallFileRel[]    findAll()
 * @method CallFileRel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallFileRelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallFileRel::class);
    }

    // /**
    //  * @return CallFileRel[] Returns an array of CallFileRel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CallFileRel
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
