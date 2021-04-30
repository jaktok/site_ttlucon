<?php

namespace App\Repository;

use App\Entity\EquipeRencontre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EquipeRencontre|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipeRencontre|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipeRencontre[]    findAll()
 * @method EquipeRencontre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipeRencontreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipeRencontre::class);
    }

    // /**
    //  * @return EquipeRencontre[] Returns an array of EquipeRencontre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EquipeRencontre
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
