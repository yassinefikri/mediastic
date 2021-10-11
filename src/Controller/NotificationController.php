<?php

namespace App\Controller;

use App\Repository\AbstractNotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notification")
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("/getAll", name="getAllNotification", options={"expose"=true})
     */
    public function index(AbstractNotificationRepository $notificationRepository): Response
    {
        return $this->json($notificationRepository->findBy(['user' => $this->getUser()]), Response::HTTP_OK, [], ['groups' => 'notif']);
    }
}
