<?php

namespace App\Repository;

use App\Entity\Rencontres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rencontres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rencontres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rencontres[]    findAll()
 * @method Rencontres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RencontresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rencontres::class);
    }

    // /**
    //  * @return Rencontres[] Returns an array of Rencontres objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Rencontres
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
