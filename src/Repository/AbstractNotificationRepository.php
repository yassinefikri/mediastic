<?php

namespace App\Repository;

use App\Entity\AbstractNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AbstractNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractNotification[]    findAll()
 * @method AbstractNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbstractNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractNotification::class);
    }

    // /**
    //  * @return AbstractNotification[] Returns an array of AbstractNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AbstractNotification
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
