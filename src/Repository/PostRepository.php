<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use App\Mapping\ConfidentialityMapping;
use App\Mapping\FriendshipMapping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private Security             $security;
    private UserRepository       $userRepository;
    private FriendshipRepository $friendshipRepository;

    public function __construct(ManagerRegistry      $registry,
                                Security             $security,
                                UserRepository       $userRepository,
                                FriendshipRepository $friendshipRepository
    ) {
        parent::__construct($registry, Post::class);

        $this->security             = $security;
        $this->userRepository       = $userRepository;
        $this->friendshipRepository = $friendshipRepository;
    }

    /**
     * @param User $user
     *
     * @return Post[]
     */
    public function getUserPosts(User $user): array
    {
        $criteria = ['createdBy' => $user];
        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();
        if ($currentUser !== $user) {
            $criteria['confidentiality'] = $this->friendshipRepository->isFriends($user, $currentUser)
                ? [ConfidentialityMapping::STATUS_PUBLIC, ConfidentialityMapping::STATUS_FRIENDS]
                : ConfidentialityMapping::STATUS_PUBLIC;
        }

        return $this->findBy($criteria, ['createdAt' => 'DESC']);
    }

    /**
     * @param User $user
     *
     * @return Post[]
     */
    public function getHomePosts(User $user): array
    {
        $friends = $this->userRepository->getUserFriends($user);

        return $this->createQueryBuilder('p')
            ->where('p.createdBy = :user')
            ->orWhere('p.createdBy IN (:friends) AND p.confidentiality IN (:confs)')
            ->setParameter('user', $user)
            ->setParameter('friends', $friends)
            ->setParameter('confs', [ConfidentialityMapping::STATUS_PUBLIC, ConfidentialityMapping::STATUS_FRIENDS])
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
