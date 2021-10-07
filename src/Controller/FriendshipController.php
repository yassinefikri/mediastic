<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\User;
use App\Manager\FriendshipManager;
use App\Repository\FriendshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Route("/friendship/{username}", name="friendship", options={"expose"=true})
     */
    public function index(User $user, Request $request, FriendshipManager $friendshipManager): JsonResponse
    {
        /**
         * @var FriendshipRepository $friendshipRepository
         */
        $friendshipRepository = $this->getDoctrine()->getManager()->getRepository(Friendship::class);
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        $friendship  = $friendshipRepository->getFriendship($user, $currentUser, true);
        $form        = $friendshipManager->getForm($friendship);

        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $friendshipManager->handleForm($form, $friendship, $user, $currentUser);

            return new JsonResponse();
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/friendship/{username}/front", name="friendship_front", options={"expose"=true})
     */
    public function front(User $user, FriendshipManager $friendshipManager): JsonResponse
    {
        /**
         * @var FriendshipRepository $friendshipRepository
         */
        $friendshipRepository = $this->getDoctrine()->getManager()->getRepository(Friendship::class);
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        $friendship  = $friendshipRepository->getFriendship($user, $currentUser, true);
        $form        = $friendshipManager->getForm($friendship);

        return new JsonResponse($this->renderView('form/friendship_form.html.twig', [
            'form' => $form->createView()
        ]));
    }

    /**
     * @Route("/friendships/{page}", name="friendships", options={"expose"=true})
     */
    public function getUserFriendships(FriendshipRepository $friendshipRepository): JsonResponse
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        $friendships = $friendshipRepository->getUserFriendships($currentUser);

        return $this->json($friendships, Response::HTTP_OK, [], ['groups' => 'friendship']);
    }

    /**
     * @Route("/friends", name="user_friends", options={"expose"=true})
     */
    public function getUserFriends(FriendshipRepository $friendshipRepository): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        return $this->json($friendshipRepository->getUserFriends($user), Response::HTTP_OK, [], ['groups' => 'json']);
    }
}
