<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=10,
     *     minMessage="Votre titre est trop court ! Il doit faire au moins {{ limit }} caractères !",
     *     max=255,
     *     maxMessage="Votre titre est trop long !"
     * )
     * @Assert\NotBlank(
     *     message="Vous devez saisir un titre !"
     * )
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *     min=250,
     *     minMessage="Vous devez saisir un minimum de texte pour votre article!"
     * )
     * @Assert\NotBlank(
     *     message="Vous devez écrire un article !"
     * )
     */
    private string $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?\DateTimeImmutable $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts")
     */
    private ?Category $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $slug;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
