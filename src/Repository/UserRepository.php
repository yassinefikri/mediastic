<?php

namespace App\Repository;

use App\Entity\User;
use App\Mapping\FriendshipMapping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param User $user
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getUserFriends(User $user)
    {
        $entityManager = $this->getEntityManager();
        $rsm           = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata(User::class, 'u');

        $sql   = 'SELECT * from `user` u where id in 
                         (SELECT IFNULL(sf.receiver_id, rf.sender_id) FROM `user` u LEFT JOIN friendship sf ON sf.sender_id = u.id AND sf.status = :status LEFT JOIN friendship rf ON rf.receiver_id = u.id AND rf.status = :status WHERE u.id = :id);';
        $query = $entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('status', FriendshipMapping::ACCEPTED, ParameterType::STRING);
        $query->setParameter('id', $user->getId(), ParameterType::INTEGER);
        return $query->getResult();
    }
}
