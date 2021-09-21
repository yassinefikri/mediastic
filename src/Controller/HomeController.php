<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home/{page}", name="home", options={"expose"=true}, requirements={"page"="^[1-9]\d*$"})
     */
    public function home(PostRepository $postRepository, int $page = 1): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $posts = $postRepository->getHomePosts($user, $page);

        return $this->json($posts, Response::HTTP_OK, [], ['groups' => 'json']);
    }
}
