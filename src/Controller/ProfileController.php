<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/{page}", name="profile", options={"expose"=true}, requirements={"page"="^[1-9]\d*$"})
     */
    public function profile(PostRepository $postRepository, int $page = 1): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $posts = $postRepository->getUserPosts($user, $page);

        return $this->json($posts, Response::HTTP_OK, [], ['groups' => 'json']);
    }

    /**
     * @Route("/{username}/{page}", name="user_profile", options={"expose"=true}, requirements={"page"="^[1-9]\d*$"})
     */
    public function index(PostRepository $postRepository, User $user, int $page = 1): JsonResponse
    {
        $posts = $postRepository->getUserPosts($user, $page);

        return $this->json($posts, Response::HTTP_OK, [], ['groups' => 'json']);
    }
}
