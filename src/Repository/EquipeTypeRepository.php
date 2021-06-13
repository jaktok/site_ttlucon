<?php

namespace App\Repository;

use App\Entity\EquipeType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EquipeType|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipeType|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipeType[]    findAll()
 * @method EquipeType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipeTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipeType::class);
    }

    // /**
    //  * @return EquipeType[] Returns an array of EquipeType objects
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

    public function findByAdultes()
    {
        return $this->createQueryBuilder('e')
        ->andWhere('e.nom NOT LIKE :val')
        ->setParameter('val', 'LUCON JEUNES%')
        ->orderBy('e.nom', 'ASC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    
    public function findOneByNom($value): ?EquipeType
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nom = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByNum($value): ?EquipeType
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.num_equipe = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
