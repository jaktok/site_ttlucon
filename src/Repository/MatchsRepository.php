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
    
    public function findDoubles()
    {
        return $this->createQueryBuilder('m')
        ->andWhere('m.matchDouble = true' )
        ->orderBy('m.id', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function findDoublesByIdJoueur($value)
    {
        return $this->createQueryBuilder('m')
        ->andWhere('m.id_joueur1 = :val or m.id_joueur2 = :val' )
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
    
    
    public function findByIdRencontreJoueurScore($idRencontre,$idJoueur,$score)
    {
        return $this->createQueryBuilder('m')
        ->andWhere('m.rencontre = :val')
        ->andWhere('m.joueur = :val2')
        ->andWhere('m.score = :val3')
        ->setParameter('val', $idRencontre)
        ->setParameter('val2', $idJoueur)
        ->setParameter('val3', $score)
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function findByIdRencontre($value)
    {
        return $this->createQueryBuilder('m')
        ->andWhere('m.rencontre = :val')
        ->setParameter('val', $value)
        ->orderBy('m.id', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    }

    
    public function findIdJoueursMatch($value)
    {//dd($value);
        return $this->createQueryBuilder('m')
        ->andWhere('m.rencontre IN (:val)')
        ->setParameter('val', $value)
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function findIdJoueursMatchEquipeSimple()
    {
        return $this->createQueryBuilder('m')
        ->andWhere('m.rencontre IS NOT NULL')
        ->andWhere('m.double1 IS NULL OR m.double2 IS NULL')
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function nettoieIdJoueurNull()
    {
         $this->createQueryBuilder('m')
        ->delete()
        ->andWhere('m.joueur IS NULL')
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
