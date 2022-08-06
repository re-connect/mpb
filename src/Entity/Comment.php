<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use App\Traits\OwnedTrait;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    use TimestampableTrait;
    use OwnedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = '';

    #[ORM\ManyToOne(targetEntity: Bug::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bug $bug = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBug(): ?Bug
    {
        return $this->bug;
    }

    public function setBug(?Bug $bug): self
    {
        $this->bug = $bug;

        return $this;
    }
}
