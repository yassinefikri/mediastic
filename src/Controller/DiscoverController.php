<?php

declare(strict_types=1);

namespace App\Controller;

use App\Resolver\UserTopicsResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Authorization;

class DiscoverController extends AbstractController
{
	/**
	 * @Route("/discover", name="discover", options={"expose"=true})
	 */
	public function __invoke(Request $request, Discovery $discovery, Authorization $authorization, UserTopicsResolver $topicsResolver): JsonResponse
	{
		$discovery->addLink($request);
        $userTopics = $topicsResolver->getUserTopics();
		$response = new JsonResponse($userTopics);
		$response->headers->setCookie(
			$authorization->createCookie($request, $userTopics)
		);
		
		return $response;
	}
}
