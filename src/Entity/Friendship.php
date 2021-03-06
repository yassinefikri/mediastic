<?php

namespace App\Entity;

use App\Mapping\FriendshipMapping;
use App\Repository\FriendshipRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FriendshipRepository::class)
 */
class Friendship
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("friendship")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sentFriendships")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("friendship")
     */
    private ?User $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="receivedFriendships")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("friendship")
     */
    private ?User $receiver;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups("friendship")
     */
    private DateTimeImmutable $sentAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups("friendship")
     */
    private ?DateTimeImmutable $answeredAt = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $editedAt = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("friendship")
     */
    private string $status;

    public function __construct()
    {
        $this->sentAt = new DateTimeImmutable();
        $this->status = FriendshipMapping::PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getSentAt(): DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getAnsweredAt(): ?DateTimeImmutable
    {
        return $this->answeredAt;
    }

    public function setAnsweredAt(DateTimeImmutable $answeredAt): self
    {
        $this->answeredAt = $answeredAt;

        return $this;
    }

    public function getEditedAt(): ?DateTimeImmutable
    {
        return $this->editedAt;
    }

    public function setEditedAt(?DateTimeImmutable $editedAt): self
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $dateTime = new DateTimeImmutable();
        if (FriendshipMapping::PENDING === $this->getStatus()) {
            $this->answeredAt = $dateTime;
        }
        $this->editedAt = $dateTime;
        $this->status   = $status;

        return $this;
    }
}
