<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Mapping\MercureEventTypesMapping;
use App\Resolver\UserTopicsResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\NotificationSeenEvent;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationEventSubscriber implements EventSubscriberInterface
{
    private SerializerInterface $serializer;

    private HubInterface $hub;

    private UserTopicsResolver $topicsResolver;

    public function __construct(SerializerInterface $serializer, HubInterface $hub, UserTopicsResolver $topicsResolver)
    {
        $this->serializer     = $serializer;
        $this->hub            = $hub;
        $this->topicsResolver = $topicsResolver;
    }

    public static function getSubscribedEvents()
    {
        return [
            NotificationSeenEvent::class => 'onNotificationSeen',
        ];
    }

    public function onNotificationSeen(NotificationSeenEvent $event)
    {
        $notification = $event->getNotification();
        $data         = $this->serializer->serialize($notification, 'json', ['groups' => 'notif']);
        $topic        = $this->topicsResolver->getNotificationsTopic($notification->getUser());
        $this->hub->publish(new Update($topic, $data, true, null, MercureEventTypesMapping::NOTIFICATION_TYPE));
    }
}
