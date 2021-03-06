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
    
    public function findByEquipe($value)
    {
        return $this->createQueryBuilder('r')
        ->andWhere('r.equipeType = :val')
        ->setParameter('val', $value)
        ->orderBy('r.date_rencontre', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function findByEquipeByPhase($id,$phase)
    {
        return $this->createQueryBuilder('r')
        ->andWhere('r.equipeType = :val')
        ->andWhere('r.phase = :val2')
        ->setParameter('val', $id)
        ->setParameter('val2', $phase)
        ->orderBy('r.date_rencontre', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function findByIdEquipe($value)
    {
        return $this->createQueryBuilder('r')
        ->select('r.id')
        ->andWhere('r.equipeType = :val')
        ->setParameter('val', $value)
        ->orderBy('r.date_rencontre', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    // pour le tableau des brules on gere les phases
    public function findByIdEquipeAndPhaseAdultes($value,$phase)
    {
        return $this->createQueryBuilder('r')
        ->select('r.id')
        ->andWhere('r.equipeType = :val')
        ->andWhere('r.phase = :val2')
        ->andWhere('r.equipeA NOT LIKE :val3')
        ->andWhere('r.equipeB NOT LIKE :val3')
        ->setParameter('val', $value)
        ->setParameter('val2', $phase)
        ->setParameter('val3', 'LUCON JEUNES%')
        ->orderBy('r.date_rencontre', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    
    public function findByNomEquipeDate($value,$date): ?Rencontres
    {//dd($value,$date);
        return $this->createQueryBuilder('e')
        ->andWhere('e.equipeA = :val')
        ->andWhere('e.date_rencontre = :dat')
        ->setParameters(array('val' => $value,'dat' => $date))
        ->getQuery()
        ->getOneOrNullResult()
        ;
    }
    
    public function findOneByFichier($value): ?Rencontres
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.fichier = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function findOneFichierByIdRencontre($value): ?Rencontres
    {
        return $this->createQueryBuilder('r')
        ->andWhere('r.id = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getOneOrNullResult()
        ;
    }
    
    
    
    public function findByPhase($value)
    {
        return $this->createQueryBuilder('r')
        ->andWhere('r.phase = :val')
        ->setParameter('val', $value)
        ->orderBy('r.date_rencontre', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    }

}
