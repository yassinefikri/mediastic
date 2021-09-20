<?php

namespace App\Repository;

use App\Entity\Friendship;
use App\Entity\User;
use App\Mapping\FriendshipMapping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    /**
     * @param User $user
     * @param User $currentUser
     *
     * @return Friendship|null
     * @throws NonUniqueResultException
     */
    public function getFriendship(User $user, User $currentUser, bool $valid = false): ?Friendship
    {
        $query = $this->createQueryBuilder('f')
            ->Where('f.sender IN (:users)')
            ->andWhere('f.receiver IN (:users)')
            ->setParameter('users', [$user, $currentUser])
            ->orderBy('f.sentAt', 'DESC')
            ->setMaxResults(1);
        if (true === $valid) {
            $query->AndWhere('f.status IN (:statuses)')
                ->setParameter('statuses', [FriendshipMapping::ACCEPTED, FriendshipMapping::PENDING]);
        }
        return $query->getQuery()
            ->getOneOrNullResult();
    }

    public function isFriends(User $user, User $currentUser): bool
    {
        $friendship = $this->createQueryBuilder('f')
            ->Where('f.sender IN (:users)')
            ->andWhere('f.receiver IN (:users)')
            ->andWhere('f.status = :status')
            ->setParameter('users', [$user, $currentUser])
            ->setParameter('status', FriendshipMapping::ACCEPTED)
            ->orderBy('f.sentAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return null !== $friendship;
    }
}
