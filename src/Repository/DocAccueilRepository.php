<?php

namespace App\Repository;

use App\Entity\DocAccueil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocAccueil|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocAccueil|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocAccueil[]    findAll()
 * @method DocAccueil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocAccueilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocAccueil::class);
    }

    // /**
    //  * @return DocAccueil[] Returns an array of DocAccueil objects
    //  */
    public function findByActif()
    {
        return $this->createQueryBuilder('p')
        ->andWhere('p.actif = true')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?DocAccueil
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
