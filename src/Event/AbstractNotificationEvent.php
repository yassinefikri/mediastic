<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\AbstractNotification;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractNotificationEvent  extends Event
{
    private AbstractNotification $notification;

    public function __construct(AbstractNotification $notification)
    {
        $this->notification = $notification;
    }

    public function getNotification(): AbstractNotification
    {
        return $this->notification;
    }
}
