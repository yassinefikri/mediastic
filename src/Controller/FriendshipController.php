<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\User;
use App\Form\AddFriendFormType;
use App\Form\AnswerFriendFormType;
use App\Form\RemoveFriendFormType;
use App\Form\SendFriendshipType;
use App\Manager\FriendshipManager;
use App\Mapping\FriendshipMapping;
use App\Repository\FriendshipRepository;
use App\Repository\UserRepository;
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
        $entityManager = $this->getDoctrine()->getManager();
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        $friendship  = $entityManager->getRepository(Friendship::class)->getFriendship($user, $currentUser, true);
        $form        = $friendshipManager->getForm($friendship, $user, $currentUser);

        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $friendshipManager->handleForm($form, $friendship, $user, $currentUser);

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/friendship/{username}/front", name="friendship_front", options={"expose"=true})
     */
    public function front(User $user, FriendshipManager $friendshipManager): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        $friendship  = $entityManager->getRepository(Friendship::class)->getFriendship($user, $currentUser, true);
        $form        = $friendshipManager->getForm($friendship, $user, $currentUser);

        return new JsonResponse($this->renderView('form/friendship_form.html.twig', [
            'form' => $form->createView()
        ]));
    }
}
