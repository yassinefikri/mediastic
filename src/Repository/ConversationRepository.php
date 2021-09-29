<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * @param User $user
     *
     * @return Conversation[]
     */
    public function getUserConversations(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.participants', 'u')
            ->where('u = :user')
            ->andWhere('c.updatedAt is NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User[] $users
     *
     * @return Conversation|null
     * @throws NonUniqueResultException
     */
    public function findByParticipants(array $users): ?Conversation
    {
        if (count($users) < 2) {
            throw new LogicException('array should have at least 2 users');
        }

        return $this->createQueryBuilder('c')
            ->where('count(c.participants) = 2')
            ->andWhere(':user1 IN (c.participants) AND :user2 IN (c.participants)')
            ->set('user1', $users[0])
            ->set('user2', $users[1])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param User $user
     *
     * @return array[]
     */
    public function getUnreadConversations(User $user)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.messages', 'm')
            ->leftJoin('c.participants', 'u')
            ->leftJoin('m.sender', 'u2')
            ->select('c.id, count(c.id) as count')
            ->where('m.sentAt is NOT NULL')
            ->andWhere('m.seenAt is NULL')
            ->andWhere('u.username = :username')
            ->andWhere('u2.username != :username')
            ->setParameter('username', $user->getUserIdentifier())
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
}
