<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends AbstractOAuthAuthenticator
{
    protected const CLIENT = 'google';

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /**
         * @var GoogleUser $userData
         */
        $userData = $this->getClient()->fetchUserFromToken($credentials);

        /**
         * @var UserRepository $userRepository
         */
        $userRepository = $this->entityManager->getRepository(User::class);
        $user           = $userRepository->findOneBy(['googleId' => $userData->getId()]);
        if (null === $user) {
            $user = $userRepository->persistNewUserFromGoogle($userData);
        }

        return $user;
    }
}