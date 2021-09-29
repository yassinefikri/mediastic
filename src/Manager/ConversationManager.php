<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ConversationManager
{
    /**
     * @var User[] $participants
     */
    private array                  $participants;
    private EntityManagerInterface $entityManager;
    private Security               $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security      = $security;
    }

    /**
     * @param array<mixed,mixed> $usernames
     *
     * @return Conversation
     */
    public function getConversation(array $usernames): ?Conversation
    {
        if (false === $this->supports($usernames)) {
            return null;
        }

        if (2 === count($usernames)) {
            /**
             * @var ConversationRepository
             */
            $conversationRepository = $this->entityManager->getRepository(Conversation::class);
            $conversation           = $conversationRepository->findByParticipants($this->participants);
            if (null === $conversation) {
                $conversation = $this->createConversation();
            }
        } else {
            $conversation = $this->createConversation();
        }

        return $conversation;
    }

    /**
     * @param array<mixed,mixed> $usernames
     *
     * @return bool
     */
    private function supports(array $usernames): bool
    {
        $count = count(array_filter($usernames, function ($username) {
            return false === is_string($username);
        }));
        if ($count > 0) {
            return false;
        }

        /**
         * @var User $user
         */
        $user = $this->security->getUser();
        if (false === in_array($user->getUserIdentifier(), $usernames)) {
            return false;
        }

        $users = $this->entityManager->getRepository(User::class)->findBy(['username' => $usernames]);
        if (count($users) !== count($usernames)) {
            return false;
        }

        $this->participants = $users;

        return true;
    }

    /**
     * @return Conversation
     */
    private function createConversation(): Conversation
    {
        $conversation = new Conversation();
        foreach ($this->participants as $participant) {
            $conversation->addParticipant($participant);
        }
        $this->entityManager->persist($conversation);
        $this->entityManager->flush();

        return $conversation;
    }
}
