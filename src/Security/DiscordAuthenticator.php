<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\DiscordClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordAuthenticator extends SocialAuthenticator
{
    private const LOGIN_ROUTE = 'connect_discord';

    private RouterInterface $router;

    private ClientRegistry $clientRegistry;

    private EntityManagerInterface $entityManager;

    public function __construct(RouterInterface $router, ClientRegistry $clientRegistry, EntityManagerInterface $entityManager)
    {
        $this->router         = $router;
        $this->clientRegistry = $clientRegistry;
        $this->entityManager  = $entityManager;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && false === in_array($request->query->get('code'), [null, '']);
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient());
    }

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

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate(self::LOGIN_ROUTE));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('default'));
    }


    private function getClient(): DiscordClient
    {
        /**
         * @var DiscordClient $client
         */
        $client = $this->clientRegistry->getClient('discord');

        return $client;
    }
}
