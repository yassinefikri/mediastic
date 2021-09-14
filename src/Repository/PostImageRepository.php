<?php

namespace App\Repository;

use App\Entity\PostImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostImage[]    findAll()
 * @method PostImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostImage::class);
    }

    // /**
    //  * @return PostImage[] Returns an array of PostImage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PostImage
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
