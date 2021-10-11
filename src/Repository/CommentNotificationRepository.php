<?php

namespace App\Repository;

use App\Entity\CommentNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentNotification[]    findAll()
 * @method CommentNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentNotification::class);
    }

    // /**
    //  * @return CommentNotification[] Returns an array of CommentNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentNotification
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
