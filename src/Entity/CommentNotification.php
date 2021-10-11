<?php

namespace App\Entity;

use App\Repository\CommentNotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentNotificationRepository::class)
 */
class CommentNotification extends AbstractNotification
{
    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="commentNotifications")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("notif")
     */
    private ?Post $post;

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
