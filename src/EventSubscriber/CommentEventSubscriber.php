<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\CommentNotification;
use App\Entity\Post;
use App\Entity\User;
use App\Event\CommentEditedEvent;
use App\Event\CommentPostedEvent;
use App\Mapping\MercureEventTypesMapping;
use App\Resolver\UserTopicsResolver;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class CommentEventSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    private Security $security;

    private SerializerInterface $serializer;

    private HubInterface $hub;

    private UserTopicsResolver $topicsResolver;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security               $security,
        SerializerInterface    $serializer,
        HubInterface           $hub,
        UserTopicsResolver     $topicsResolver
    ) {
        $this->entityManager  = $entityManager;
        $this->security       = $security;
        $this->serializer     = $serializer;
        $this->hub            = $hub;
        $this->topicsResolver = $topicsResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CommentPostedEvent::class => 'handleNewComment',
            CommentEditedEvent::class => 'handleEditedComment',
        ];
    }

    public function handleNewComment(CommentPostedEvent $event): void
    {
        /**
         * @var Post $post
         */
        $post = $event->getComment()->getPost();
        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();
        /**
         * @var User $postOwner
         */
        $postOwner = $post->getCreatedBy();
        if ($currentUser !== $postOwner) {
            $notification = $this->entityManager->getRepository(CommentNotification::class)->findOneBy(['post' => $post]);
            if (null === $notification) {
                $notification = new CommentNotification();
                $notification->setPost($post);

                $notification->setUser($postOwner);
            } else {
                $notification->setCreatedAt(new DateTimeImmutable());
            }
            $notification->setContent("{$currentUser->getFirstName()} {$currentUser->getLastName()} (@{$currentUser->getUserIdentifier()}) commented on your post");
            $this->entityManager->persist($notification);
            $this->entityManager->flush();

            $this->sendUpdateToClient($notification);
        }
    }

    public function handleEditedComment(CommentEditedEvent $event): void
    {
        // TODO implement method
    }

    private function sendUpdateToClient(CommentNotification $notification): void
    {
        /**
         * @var Post $post
         */
        $post   = $notification->getPost();
        $data   = $this->serializer->serialize($notification, 'json', ['groups' => 'notif']);
        $topic  = $this->topicsResolver->getNotificationsTopic($post->getCreatedBy());
        $update = new Update($topic, (string)json_encode($data), true, null, MercureEventTypesMapping::NOTIFICATION_TYPE);

        $this->hub->publish($update);
    }
}
