<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use App\Mapping\ConfidentialityMapping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    private Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Post::class);

        $this->security = $security;
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
        if ($currentUser->getUserIdentifier() !== $user->getUserIdentifier()) {
            $criteria['confidentiality'] = ConfidentialityMapping::STATUS_PUBLIC;
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
        $criteria = ['createdBy' => $user];
        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();
        if ($currentUser->getUserIdentifier() !== $user->getUserIdentifier()) {
            $criteria['confidentiality'] = ConfidentialityMapping::STATUS_PUBLIC;
        }

        return $this->findBy($criteria, ['createdAt' => 'DESC']);
    }
}
