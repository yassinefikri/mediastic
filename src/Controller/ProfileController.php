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
     * @Route("/", name="profile", options={"expose"=true})
     */
    public function profile(PostRepository $postRepository): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $posts = $postRepository->getUserPosts($user);

        return $this->json($posts, Response::HTTP_OK, [], ['groups' => 'json']);
    }

    /**
     * @Route("/{username}", name="user_profile", options={"expose"=true})
     */
    public function index(PostRepository $postRepository, User $user): JsonResponse
    {
        $posts = $postRepository->getUserPosts($user);

        return $this->json($posts, Response::HTTP_OK, [], ['groups' => 'json']);
    }
}
