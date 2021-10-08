<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Comment;

abstract class AbstractCommentEvent
{
    private Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
