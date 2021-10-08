<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Message;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractMessageEvent extends Event
{
    private Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }
}
