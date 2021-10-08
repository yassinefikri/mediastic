<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Conversation;
use App\Event\MessageSentEvent;
use App\Resolver\UserTopicsResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

class MessageEventSubscriber implements EventSubscriberInterface
{
    private HubInterface $hub;

    private SerializerInterface $serializer;

    private UserTopicsResolver $topicsResolver;

    public function __construct(SerializerInterface $serializer, HubInterface $hub, UserTopicsResolver $topicsResolver)
    {
        $this->hub            = $hub;
        $this->serializer     = $serializer;
        $this->topicsResolver = $topicsResolver;
    }

    /**
     * @return mixed[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MessageSentEvent::class => 'handleMessageSent'
        ];
    }

    public function handleMessageSent(MessageSentEvent $event): void
    {
        $message = $event->getMessage();
        /**
         * @var Conversation $conversation
         */
        $conversation = $message->getConversation();
        $messageData  = $this->serializer->serialize($message, 'json', ['groups' => 'message']);
        $data         = ['status' => 'newMessage', 'message' => $messageData];
        $topics       = [];
        foreach ($conversation->getParticipants() as $participant) {
            $topics[] = $this->topicsResolver->getChatTopic($participant);
        }
        $this->hub->publish(new Update($topics, (string)json_encode($data), true, null, 'chat'));
    }
}
