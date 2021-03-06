<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordAuthenticator extends AbstractOAuthAuthenticator
{
    protected const CLIENT = 'discord';

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /**
         * @var DiscordResourceOwner $userData
         */
        $userData = $this->getClient()->fetchUserFromToken($credentials);

        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $this->entityManager->getRepository(User::class);
        $user           = $userRepository->findOneBy(['discordId' => $userData->getId()]);
        if (null === $user) {
            $user = $userRepository->persistNewUserFromDiscord($userData);
        }

        return $user;
    }
}
