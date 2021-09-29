<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Manager\ConversationManager;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conversation")
 */
class ConversationController extends AbstractController
{
    /**
     * @Route("/getAll", name="get_conversations", options={"expose"=true})
     */
    public function getAllConversations(ConversationRepository $conversationRepository): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        return $this->json($conversationRepository->getUserConversations($user), Response::HTTP_OK, [], ['groups' => 'json']);
    }

    /**
     * @Route("/get", name="get_conversation", options={"expose"=true})
     */
    public function getConversation(Request $request, ConversationManager $conversationManager): JsonResponse
    {
        $usernames    = $request->request->get('participants');
        $conversation = $conversationManager->getConversation($usernames);
        if (null === $conversation) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return $this->json($conversation, Response::HTTP_OK, [], ['groups' => 'json']);
    }

    /**
     * @Route("/getMessages/{id}/{page}", name="get_conversation_messages", options={"expose"=true},
     *                                    requirements={"page"="^[1-9]\d*$"})
     * @IsGranted("READ_MESSAGES", subject="conversation")
     */
    public function getConversationMessages(Conversation $conversation, int $page = 1): JsonResponse
    {
        /**
         * @var MessageRepository $messagesRepository
         */
        $messagesRepository = $this->getDoctrine()->getManager()->getRepository(Message::class);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $messagesRepository->setMessagesSeen($conversation, $user);

        return $this->json($messagesRepository->getMessages($conversation, $page), Response::HTTP_OK, [], ['groups' => 'message']);
    }

    /**
     * @Route("/getUnread", name="get_unread_conversations", options={"expose"=true})
     */
    public function getUnreadConversations(ConversationRepository $conversationRepository): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        return new JsonResponse($conversationRepository->getUnreadConversations($user));
    }
}
