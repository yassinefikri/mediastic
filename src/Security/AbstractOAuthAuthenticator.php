<?php

declare(strict_types=1);

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

abstract class AbstractOAuthAuthenticator extends SocialAuthenticator
{
    protected const CLIENT = '';
    protected const LOGIN_ROUTE_PREFIX = 'connect_';

    protected RouterInterface $router;

    protected ClientRegistry $clientRegistry;

    protected EntityManagerInterface $entityManager;

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
        return $this->getLoginRoute() === $request->attributes->get('_route')
            && false === in_array($request->query->get('code'), [null, '']);
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient());
    }

    protected function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient(static::CLIENT);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate($this->getLoginRoute()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('default'));
    }

    protected function getLoginRoute(): string
    {
        return self::LOGIN_ROUTE_PREFIX.static::CLIENT;
    }
}
