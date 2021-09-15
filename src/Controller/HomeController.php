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
     * @Route("/home", name="home", options={"expose"=true})
     */
    public function home(PostRepository $postRepository): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $posts = $postRepository->getHomePosts($user);

        return $this->json($posts, Response::HTTP_OK, [], ['groups' => 'json']);
    }
}
