<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Event\MessageEditedEvent;
use App\Event\MessageSentEvent;
use App\Form\MessageFormType;
use App\Manager\SeenManager;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Route("/getForm", name="message_sending_front", options={"expose"=true})
     * @Route("/getForm/{id}", name="message_edit_front", options={"expose"=true}, requirements={"id"="^[1-9]\d*$"})
     */
    public function getForm(string $_route, Message $message = null): JsonResponse
    {
        if ('edit_comment_front' === $_route) {
            if (null === $message) {
                throw new NotFoundHttpException();
            } else {
                $this->denyAccessUnlessGranted('MESSAGE_EDIT', $message);
            }
        }
        $form = $this->createForm(MessageFormType::class, $message);

        return new JsonResponse($this->renderView('form/message_form.html.twig', [
            'form' => $form->createView()
        ]));
    }

    /**
     * @Route("/sendMessage/{id}", name="message_sending", options={"expose"=true}, methods={"POST"}, requirements={"id"="^[1-9]\d*$"})
     * @IsGranted("SEND_MESSAGE", subject="conversation")
     */
    public function sendMessage(Conversation $conversation, Request $request, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $message = new Message();
        $form    = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            /**
             * @var User $user
             */
            $user = $this->getUser();
            $message->setSender($user);
            $message->setConversation($conversation);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $conversation->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $eventDispatcher->dispatch(new MessageSentEvent($message));

            return $this->json($message, Response::HTTP_OK, [], ['groups' => 'message']);
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/seen/{id}", name="set_message_seen", options={"expose"=true}, methods={"POST"}, requirements={"id"="^[1-9]\d*$"})
     */
    public function setMessageSeen(Message $message, SeenManager $manager): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        /**
         * @var Conversation $conversation
         */
        $conversation = $message->getConversation();
        if ($conversation->getParticipants()->contains($user)
            && $message->getSender() !== $user
            && !$message->getSeenBy()->contains($user)
        ) {
            $message->addSeenBy($user);
            $this->getDoctrine()->getManager()->flush();
            $manager->messageSeenUpdate2Clients([$message]);

            return new JsonResponse();
        }

        return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/{id}/edit", name="edit_message", options={"expose"=true}, methods={"POST"}, requirements={"id"="^[1-9]\d*$"})
     * @IsGranted("MESSAGE_EDIT", subject="message")
     */
    public function editMessage(Request $request, Message $message, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $eventDispatcher->dispatch(new MessageEditedEvent($message));

            return new JsonResponse();
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }
}
