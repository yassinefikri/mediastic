<?php

namespace App\Repository;

use App\Entity\AbstractNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method AbstractNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractNotification[]    findAll()
 * @method AbstractNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbstractNotificationRepository extends ServiceEntityRepository
{
    use RepositoryTrait;

    private const PAGE_SIZE = 10;

    private Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, AbstractNotification::class);
        $this->security = $security;
    }

    /**
     * @param int $page
     *
     * @return AbstractNotification[]
     */
    public function getNotificationsByPage(int $page): array
    {
        $this->validatePageNumber($page);

        return $this->findBy(['user' => $this->security->getUser()], ['createdAt' => 'DESC'], self::PAGE_SIZE, self::PAGE_SIZE * ($page - 1));
    }
}
