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

    public function getUserConversation(User $user, int $id): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.participants', 'u')
            ->where('u = :user')
            ->andWhere('c.updatedAt is NOT NULL')
            ->setParameter('user', $user)
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
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
        if ($count < 2) {
            throw new LogicException('array should contain at least 2 users');
        }

        $entityManager = $this->getEntityManager();
        $rsm           = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata(Conversation::class, 'c1');

        $count = count($users);
        $sql   = 'SELECT * FROM `conversation` c1 WHERE c1.id IN (
            SELECT c.id from `conversation` c INNER JOIN `conversation_user` cu ON c.id = cu.conversation_id INNER JOIN `user` u ON u.id = cu.user_id WHERE ';

        for ($i = 0; $i < $count; $i++) {
            if (0 !== $i) {
                $sql .= "AND ";
            }
            $sql .= "c.id IN (SELECT conversation_id from `conversation_user` where user_id = :user{$i}) ";
        }
        $sql   .= "GROUP BY c.id  
            HAVING count(*) = {$count}
        )";
        $query = $entityManager->createNativeQuery($sql, $rsm);
        for ($i = 0; $i < $count; $i++) {
            $query->setParameter('user'.$i, $users[$i]->getId(), Types::INTEGER);
        }

        return $query->getOneOrNullResult();
    }

    /**
     * @param User $user
     *
     * @return array[]
     */
    public function getUnreadConversations(User $user)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('count', 'count');

        $sql = 'SELECT cf.id, count(cf.id) AS count FROM `conversation` cf INNER JOIN `message` mf ON mf.conversation_id = cf.id WHERE mf.id IN (
                    SELECT m.id FROM `message` m INNER JOIN `conversation` c ON m.conversation_id = c.id INNER JOIN `user` u ON m.sender_id = u.id WHERE c.id IN (
                        SELECT c1.id from `conversation` c1 INNER JOIN `conversation_user` cu ON cu.conversation_id = c1.id WHERE cu.user_id = :user
                    )
                    AND m.id NOT IN (
                        SELECT mu.message_id FROM `message_user` mu WHERE mu.user_id = :user
                    ) 
                    AND u.id != :user
                ) GROUP BY cf.id';

        $query = $entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('user', $user->getId(), Types::INTEGER);

        return $query->getResult();
    }
}
