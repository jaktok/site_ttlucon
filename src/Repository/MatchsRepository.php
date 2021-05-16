<?php

namespace App\Repository;

use App\Entity\Matchs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Matchs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matchs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matchs[]    findAll()
 * @method Matchs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matchs::class);
    }

    // /**
    //  * @return Matchs[] Returns an array of Matchs objects
    //  */
    
    public function findByIdCompet($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.competition = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findByIdCompetJoueur($idCompet,$idJoueur)
    {
        return $this->createQueryBuilder('m')
        ->andWhere('m.competition = :val')
        ->andWhere('m.joueur = :val2')
        ->setParameter('val', $idCompet)
        ->setParameter('val2', $idJoueur)
        ->getQuery()
        ->getResult()
        ;
    }
    

    /*
    public function findOneBySomeField($value): ?Matchs
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
