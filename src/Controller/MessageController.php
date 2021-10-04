<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageFormType;
use App\Manager\SeenManager;
use App\Resolver\UserTopicsResolver;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Route("/getForm", name="message_sending_front", options={"expose"=true})
     */
    public function getForm(): JsonResponse
    {
        $form = $this->createForm(MessageFormType::class);

        return new JsonResponse($this->renderView('form/message_form.html.twig', [
            'form' => $form->createView()
        ]));
    }

    /**
     * @Route("/sendMessage/{id}", name="message_sending", options={"expose"=true})
     * @IsGranted("SEND_MESSAGE", subject="conversation")
     */
    public function sendMessage(
        Conversation        $conversation,
        Request             $request,
        HubInterface        $hub,
        UserTopicsResolver  $topicsResolver,
        SerializerInterface $serializer
    ): JsonResponse {
        $form = $this->createForm(MessageFormType::class);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            /**
             * @var User $user
             */
            $user    = $this->getUser();
            $message = new Message();
            $message->setConversation($conversation);
            $message->setSender($user);
            $message->setContent($form->get('content')->getData());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $conversation->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $messageData = $serializer->serialize($message, 'json', ['groups' => 'message']);
            $data        = ['status' => 'newMessage', 'message' => $messageData];
            $topics      = [];
            foreach ($conversation->getParticipants() as $participant) {
                $topics[] = $topicsResolver->getChatTopic($participant);
            }
            $hub->publish(new Update($topics, (string)json_encode($data), true, null, 'chat'));

            return $this->json($message, Response::HTTP_OK, [], ['groups' => 'message']);
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/seen/{id}", name="set_message_seen", options={"expose"=true}, requirements={"id"="^[1-9]\d*$"})
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
}
