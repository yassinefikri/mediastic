<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Types;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    use RepositoryTrait;

    private const PAGE_SIZE = 15;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param Conversation $conversation
     * @param int          $page
     *
     * @return Message[]
     */
    public function getMessages(Conversation $conversation, int $page): array
    {
        $this->validatePageNumber($page);

        return $this->findBy(['conversation' => $conversation], ['sentAt' => 'DESC'], self::PAGE_SIZE, self::PAGE_SIZE * ($page - 1));
    }
}
