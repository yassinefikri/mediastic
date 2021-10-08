<?php

namespace App\Entity;

use App\Repository\PostImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PostImageRepository::class)
 */
class PostImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("json")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="postImages")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Post $post;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $imageName;

    public function __construct()
    {
        $this->imageName = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }
}
