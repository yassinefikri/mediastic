<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Length(
     *      min = 6,
     *      max = 50,
     *      minMessage = "Your username must be at least {{ limit }} characters long",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters"
     * )
     * @Groups({"json","friendship","message"})
     */
    private string $username;

    /**
     * @ORM\Column(type="json")
     * @var string[] $roles
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"json","friendship","message"})
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"json","friendship","message"})
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $cover;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="createdBy", orphanRemoval=true)
     */
    private Collection $posts;

    /**
     * @ORM\OneToMany(targetEntity=Friendship::class, mappedBy="sender", orphanRemoval=true)
     */
    private Collection $sentFriendships;

    /**
     * @ORM\OneToMany(targetEntity=Friendship::class, mappedBy="receiver", orphanRemoval=true)
     */
    private Collection $receivedFriendships;

    /**
     * @ORM\ManyToMany(targetEntity=Conversation::class, mappedBy="participants")
     */
    private Collection $conversations;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sender", orphanRemoval=true)
     */
    private Collection $messages;

    public function __construct()
    {
        $this->posts               = new ArrayCollection();
        $this->sentFriendships     = new ArrayCollection();
        $this->receivedFriendships = new ArrayCollection();
        $this->conversations       = new ArrayCollection();
        $this->messages            = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setCreatedBy($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCreatedBy() === $this) {
                $post->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getSentFriendships(): Collection
    {
        return $this->sentFriendships;
    }

    public function addSentFriendship(Friendship $sentFriendship): self
    {
        if (!$this->sentFriendships->contains($sentFriendship)) {
            $this->sentFriendships[] = $sentFriendship;
            $sentFriendship->setSender($this);
        }

        return $this;
    }

    public function removeSentFriendship(Friendship $sentFriendship): self
    {
        if ($this->sentFriendships->removeElement($sentFriendship)) {
            // set the owning side to null (unless already changed)
            if ($sentFriendship->getSender() === $this) {
                $sentFriendship->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getReceivedFriendships(): Collection
    {
        return $this->receivedFriendships;
    }

    public function addReceivedFriendship(Friendship $receivedFriendship): self
    {
        if (!$this->receivedFriendships->contains($receivedFriendship)) {
            $this->receivedFriendships[] = $receivedFriendship;
            $receivedFriendship->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedFriendship(Friendship $receivedFriendship): self
    {
        if ($this->receivedFriendships->removeElement($receivedFriendship)) {
            // set the owning side to null (unless already changed)
            if ($receivedFriendship->getReceiver() === $this) {
                $receivedFriendship->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Conversation[]
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->addParticipant($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }
}
