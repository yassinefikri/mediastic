<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConversationManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string[] $usernames
     *
     * @return Conversation
     */
    public function getConversation(array $usernames): ?Conversation
    {
        $users = $this->entityManager->getRepository(User::class)->findBy(['username' => $usernames]);
        if (2 === count($usernames)) {
            /**
             * @var ConversationRepository
             */
            $conversationRepository = $this->entityManager->getRepository(Conversation::class);
            $conversation           = $conversationRepository->findByParticipants($users);
            if (null === $conversation) {
                $conversation = $this->createConversation($users);
            }
        } else {
            $conversation = $this->createConversation($users);
        }

        return $conversation;
    }

    /**
     * @param User[] $users
     *
     * @return Conversation
     */
    private function createConversation(array $users): Conversation
    {
        $conversation = new Conversation();
        foreach ($users as $participant) {
            $conversation->addParticipant($participant);
        }
        $this->entityManager->persist($conversation);
        $this->entityManager->flush();

        return $conversation;
    }
}
