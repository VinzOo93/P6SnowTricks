<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }


    public function findByLastDate()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.dateAdded < CURRENT_TIMESTAMP()')
            ->orderBy('t.dateAdded', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
        ;
    }

    public  function findNextDate(array $id){
        return $this->createQueryBuilder('t')
            ->setParameter('id', array_values($id))
            ->andWhere('t.dateAdded < CURRENT_TIMESTAMP()')
            ->andWhere('id < :id')
            ->orderBy('t.dateAdded', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Trick
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
