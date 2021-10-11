<?php

namespace App\Entity;

use App\Repository\AbstractNotificationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AbstractNotificationRepository::class)
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"comment" = "CommentNotification"})
 */
abstract class AbstractNotification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("notif")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="abstractNotifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups("notif")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="text")
     * @Groups("notif")
     */
    private string $content;

    /**
     * @ORM\Column(type="boolean")
     * @Groups("notif")
     */
    private bool $seen;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->seen = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(bool $seen): self
    {
        $this->seen = $seen;

        return $this;
    }
}
