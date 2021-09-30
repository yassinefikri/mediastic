<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Form\SearchConversationType;
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
    use ControllerTrait;

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
        $form = $this->createForm(SearchConversationType::class);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $conversation = $conversationManager->getConversation(json_decode($form->get('participants')->getData()));
            if (null !== $conversation) {
                return $this->json($conversation, Response::HTTP_OK, [], ['groups' => 'json']);
            }
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/get/front", name="get_conversation_front", options={"expose"=true})
     */
    public function getConversationFront(): JsonResponse
    {
        $form = $this->createForm(SearchConversationType::class);

        return new JsonResponse($this->renderView('form/navbar_search.html.twig', [
            'form' => $form->createView()
        ]));
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
