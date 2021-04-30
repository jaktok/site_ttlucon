<?php

namespace App\Repository;

use App\Entity\InfosClub;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InfosClub|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfosClub|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfosClub[]    findAll()
 * @method InfosClub[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfosClubRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfosClub::class);
    }

    // /**
    //  * @return InfosClub[] Returns an array of InfosClub objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InfosClub
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
