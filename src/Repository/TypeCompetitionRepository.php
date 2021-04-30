<?php

namespace App\Repository;

use App\Entity\TypeCompetition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeCompetition|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeCompetition|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeCompetition[]    findAll()
 * @method TypeCompetition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeCompetitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeCompetition::class);
    }

    // /**
    //  * @return TypeCompetition[] Returns an array of TypeCompetition objects
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
    public function findOneBySomeField($value): ?TypeCompetition
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
