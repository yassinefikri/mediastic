<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserTopicsResolver
{
    private const TOPIC_PREFIX = '/';
    private const TOPICS       = [
        'chat'         => 'chat',
        'friendship'   => 'friendship',
        'notification' => 'notification'
    ];

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return string[]
     */
    public function getUserTopics(User $user = null): array
    {
        return [
            $this->getChatTopic($user),
            $this->getNotificationsTopic($user),
            $this->getFriendshipTopic($user)
        ];
    }

    public function getChatTopic(User $user = null): string
    {
        if (null === $user) {
            /**
             * @var User $user
             */
            $user = $this->security->getUser();
        }
        return self::TOPIC_PREFIX.$user->getUserIdentifier().'/'.self::TOPICS['chat'];
    }

    public function getNotificationsTopic(User $user = null): string
    {
        if (null === $user) {
            /**
             * @var User $user
             */
            $user = $this->security->getUser();
        }
        return self::TOPIC_PREFIX.$user->getUserIdentifier().'/'.self::TOPICS['notification'];
    }

    public function getFriendshipTopic(User $user = null): string
    {
        if (null === $user) {
            /**
             * @var User $user
             */
            $user = $this->security->getUser();
        }
        return self::TOPIC_PREFIX.$user->getUserIdentifier().'/'.self::TOPICS['friendship'];
    }
}
