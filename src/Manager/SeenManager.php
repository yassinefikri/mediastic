<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Mapping\MercureEventTypesMapping;
use App\Resolver\UserTopicsResolver;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class SeenManager
{
    private HubInterface $hub;

    private UserTopicsResolver $topicsResolver;

    private Security $security;

    private SerializerInterface $serializer;

    public function __construct(HubInterface $hub, UserTopicsResolver $topicsResolver, Security $security, SerializerInterface $serializer)
    {
        $this->hub            = $hub;
        $this->topicsResolver = $topicsResolver;
        $this->security       = $security;
        $this->serializer     = $serializer;
    }

    /**
     * @param Message[] $messages
     */
    public function messageSeenUpdate2Clients(array $messages): void
    {
        $topics = [];
        $data   = [];
        /**
         * @var User $user
         */
        $user          = $this->security->getUser();
        $conversations = [];
        foreach ($messages as $message) {
            /**
             * @var Conversation $conversation
             */
            $conversation = $message->getConversation();
            /**
             * @var int $conversationId
             */
            $conversationId = $conversation->getId();
            if (false === array_key_exists($conversationId, $data)) {
                $data[$conversationId] = [];
            }
            if (false === array_key_exists($conversationId, $conversations)) {
                $conversations[$conversationId] = $conversation;
            }
            $data[$conversationId][] = ['user' => $this->serializer->serialize($user, 'json', ['groups' => 'message']), 'message' => $message->getId()];
        }
        if ([] !== $data) {
            foreach ($conversations as $conversation) {
                foreach ($conversation->getParticipants() as $participant) {
                    if (false === array_key_exists($participant->getUserIdentifier(), $topics)) {
                        $topics[$participant->getUserIdentifier()] = $this->topicsResolver->getChatTopic($participant);
                    }
                }
            }

            $update = new Update($topics, (string)json_encode($data), true, null, MercureEventTypesMapping::SEEN_TYPE);
            $this->hub->publish($update);
        }
    }
}
