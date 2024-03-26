<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ApiResource()]
#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getBooks", "getAuthors"])]
    private ?int $id = null;
    #[Groups(["getBooks", "getAuthors"])]
    #[Assert\NotBlank(message: 'title.required')]
    #[Assert\Length(min: 1, max: 255, minMessage: 'Title is too short', maxMessage: 'Title is too long')]
    #[ORM\Column(length: 255)]
    private ?string $title = null;
    #[Groups(["getBooks", "getAuthors"])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $coverText = null;
    #[Groups(["getBooks"])]
    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Author $author = null;

    #[Groups(["getBooks", "getAuthors"])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCoverText(): ?string
    {
        return $this->coverText;
    }

    public function setCoverText(?string $coverText): static
    {
        $this->coverText = $coverText;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
