<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("message")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("message")
     */
    private ?User $sender;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups("message")
     */
    private DateTimeImmutable $sentAt;

    /**
     * @ORM\Column(type="text")
     * @Groups("message")
     * @Assert\NotBlank
     */
    private string $content;

    /**
     * @ORM\ManyToOne(targetEntity=Conversation::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("message")
     */
    private ?Conversation $conversation;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups("message")
     */
    private ?DateTimeImmutable $seenAt;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="seenMessages")
     * @Groups("message")
     */
    private Collection $seenBy;

    public function __construct()
    {
        $this->sentAt = new DateTimeImmutable();
        $this->seenAt = null;
        $this->seenBy = new ArrayCollection();
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

    public function getSentAt(): DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;

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

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function getSeenAt(): ?DateTimeImmutable
    {
        return $this->seenAt;
    }

    public function setSeenAt(?DateTimeImmutable $seenAt): self
    {
        $this->seenAt = $seenAt;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSeenBy(): Collection
    {
        return $this->seenBy;
    }

    public function addSeenBy(User $seenBy): self
    {
        if (!$this->seenBy->contains($seenBy)) {
            $this->seenBy[] = $seenBy;
            /**
             * @var Conversation $conversation
             */
            $conversation = $this->getConversation();
            if ($this->seenBy->count() === $conversation->getParticipants()->count() - 1) {
                $this->seenAt = new DateTimeImmutable();
            }
        }

        return $this;
    }

    public function removeSeenBy(User $seenBy): self
    {
        $this->seenBy->removeElement($seenBy);

        return $this;
    }
}
