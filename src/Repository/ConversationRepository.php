<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
        $count = count($users);
        if ($count !== 2) {
            throw new LogicException('array should have exactly 2 users');
        }

        $entityManager = $this->getEntityManager();
        $rsm           = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata(Conversation::class, 'c1');

        $sql   = 'SELECT * FROM `conversation` c1 WHERE c1.id IN (
            SELECT c.id from `conversation` c INNER JOIN `conversation_user` cu ON c.id = cu.conversation_id INNER JOIN `user` u ON u.id = cu.user_id WHERE c.id IN (SELECT conversation_id from `conversation_user` where user_id = :user1) AND c.id IN (SELECT conversation_id from `conversation_user` where user_id = :user2) GROUP BY c.id  HAVING count(*) = 2
        )';
        $query = $entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('user1', $users[0]->getId(), Types::INTEGER);
        $query->setParameter('user2', $users[1]->getId(), Types::INTEGER);

        return $query->getOneOrNullResult();
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
