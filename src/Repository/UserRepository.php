<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private const SEARCH_RESULT_MAX = 5;

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
     * @param string $search
     *
     * @return User[]
     */
    public function searchUsers(string $search): array
    {
        $entityManager = $this->getEntityManager();
        $rsm           = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata(User::class, 'u');

        $sql   = 'SELECT * from `user` u where username REGEXP :query LIMIT '.self::SEARCH_RESULT_MAX;
        $query = $entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('query', $search, Types::STRING);

        return $query->getResult();
    }

    public function persistNewUserFromDiscord(DiscordResourceOwner $userData): User
    {
        /**
         * @var string $email
         */
        $email = $userData->getEmail();
        /**
         * @var string $username
         */
        $username = $userData->getUsername();

        $user = new User();
        $user->setUsername('user-' . uniqid());
        $user->setEmail($email);
        $user->setDiscordId($userData->getId());
        $user->setFirstName($username);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
