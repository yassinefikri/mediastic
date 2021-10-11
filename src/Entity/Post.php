<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Mapping\ConfidentialityMapping;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"json","notif"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("json")
     * @Assert\NotBlank()
     */
    private string $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups("json")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("json")
     */
    private ?User $createdBy;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("json")
     * @Assert\Choice(choices=ConfidentialityMapping::confs, message="Choose a valid conf.")
     */
    private string $confidentiality;

    /**
     * @ORM\OneToMany(targetEntity=PostImage::class, mappedBy="post", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups("json")
     */
    private Collection $postImages;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post", orphanRemoval=true)
     */
    private Collection $comments;

    /**
     * @ORM\OneToMany(targetEntity=CommentNotification::class, mappedBy="post", orphanRemoval=true)
     */
    private Collection $commentNotifications;

    public function __construct()
    {
        $this->postImages = new ArrayCollection();
        $this->createdAt  = new DateTimeImmutable();
        $this->comments   = new ArrayCollection();
        $this->commentNotifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getConfidentiality(): string
    {
        return $this->confidentiality;
    }

    public function setConfidentiality(string $confidentiality): self
    {
        $this->confidentiality = $confidentiality;

        return $this;
    }

    /**
     * @return Collection|PostImage[]
     */
    public function getPostImages(): Collection
    {
        return $this->postImages;
    }

    public function addPostImage(PostImage $postImage): self
    {
        if (!$this->postImages->contains($postImage)) {
            $this->postImages[] = $postImage;
            $postImage->setPost($this);
        }

        return $this;
    }

    public function removePostImage(PostImage $postImage): self
    {
        if ($this->postImages->removeElement($postImage)) {
            // set the owning side to null (unless already changed)
            if ($postImage->getPost() === $this) {
                $postImage->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CommentNotification[]
     */
    public function getCommentNotifications(): Collection
    {
        return $this->commentNotifications;
    }

    public function addCommentNotification(CommentNotification $commentNotification): self
    {
        if (!$this->commentNotifications->contains($commentNotification)) {
            $this->commentNotifications[] = $commentNotification;
            $commentNotification->setPost($this);
        }

        return $this;
    }

    public function removeCommentNotification(CommentNotification $commentNotification): self
    {
        if ($this->commentNotifications->removeElement($commentNotification)) {
            // set the owning side to null (unless already changed)
            if ($commentNotification->getPost() === $this) {
                $commentNotification->setPost(null);
            }
        }

        return $this;
    }
}
