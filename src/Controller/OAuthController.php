<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/connect")
 */
class OAuthController extends AbstractController
{
    /**
     * @Route("/discord", name="connect_discord")
     */
    public function discordOAuthConnect(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry->getClient('discord')->redirect(['identify', 'email'], []);
    }

    /**
     * @Route("/google", name="connect_google")
     */
    public function googleOAuthConnect(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry->getClient('google')->redirect(
            [
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
                'openid'
            ], []);
    }
}
