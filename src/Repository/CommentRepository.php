<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }


    public  function findComments(string  $trickId, string $comId){
        return $this->createQueryBuilder('c')
            ->setParameter('Tid', $trickId)
            ->andWhere('c.trick = :Tid')
            ->setParameter('Cid', $comId)
            ->andWhere('c.id < :Cid')
            ->orderBy('c.dateAdded', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;


    }
}
