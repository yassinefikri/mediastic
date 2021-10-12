<?php

namespace App\Controller;

use App\Entity\AbstractNotification;
use App\Event\NotificationSeenEvent;
use App\Repository\AbstractNotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notification")
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("/get/{page}", name="getNotifications", options={"expose"=true}, requirements={"page"="^[1-9]\d*$"})
     */
    public function index(AbstractNotificationRepository $notificationRepository, int $page = 1): JsonResponse
    {
        return $this->json($notificationRepository->getNotificationsByPage($page), Response::HTTP_OK, [], ['groups' => 'notif']);
    }

    /**
     * @Route("/setSeen/{id}", name="setNotificationSeen", options={"expose"=true}, methods={"POST"}, requirements={"id"="^[1-9]\d*$"})
     * @IsGranted("NOTIFICATION_UPDATE_SEEN", subject="notification")
     */
    public function setSeen(AbstractNotification $notification, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $notification->setSeen(true);
        $this->getDoctrine()->getManager()->flush();

        $eventDispatcher->dispatch(new NotificationSeenEvent($notification));

        return new JsonResponse();
    }

    /**
     * @Route("/remove/{id}", name="removeNotification", options={"expose"=true}, methods={"POST"}, requirements={"id"="^[1-9]\d*$"})
     * @IsGranted("NOTIFICATION_REMOVE", subject="notification")
     */
    public function removeNotification(AbstractNotification $notification): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($notification);
        $entityManager->flush();

        return new JsonResponse();
    }
}
