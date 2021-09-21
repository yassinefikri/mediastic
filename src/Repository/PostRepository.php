<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use App\Mapping\ConfidentialityMapping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Component\Security\Core\Security;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private const PAGE_SIZE = 10;

    private Security             $security;
    private FriendshipRepository $friendshipRepository;

    public function __construct(ManagerRegistry      $registry,
                                Security             $security,
                                FriendshipRepository $friendshipRepository
    ) {
        parent::__construct($registry, Post::class);

        $this->security             = $security;
        $this->friendshipRepository = $friendshipRepository;
    }

    /**
     * @param User $user
     * @param int  $page
     *
     * @return Post[]
     */
    public function getUserPosts(User $user, int $page): array
    {
        $this->validatePageNumber($page);
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

        return $this->findBy($criteria, ['createdAt' => 'DESC'], self::PAGE_SIZE, self::PAGE_SIZE * ($page - 1));
    }

    /**
     * @param User $user
     * @param int  $page
     *
     * @return Post[]
     */
    public function getHomePosts(User $user, int $page): array
    {
        $this->validatePageNumber($page);
        $friends = $this->friendshipRepository->getUserFriends($user);

        return $this->createQueryBuilder('p')
            ->where('p.createdBy = :user')
            ->orWhere('p.createdBy IN (:friends) AND p.confidentiality IN (:confs)')
            ->setParameter('user', $user)
            ->setParameter('friends', $friends)
            ->setParameter('confs', [ConfidentialityMapping::STATUS_PUBLIC, ConfidentialityMapping::STATUS_FRIENDS])
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(self::PAGE_SIZE * ($page - 1))
            ->setMaxResults(self::PAGE_SIZE)
            ->getQuery()
            ->getResult();
    }

    private function validatePageNumber(int $page): void
    {
        if ($page < 1) {
            throw new LogicException('Page number should be positive');
        }
    }
}
