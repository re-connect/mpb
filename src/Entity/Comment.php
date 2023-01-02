<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use App\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $content = '';

    #[ORM\ManyToOne(targetEntity: Bug::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Bug $bug = null;

    #[ORM\ManyToOne(targetEntity: Feature::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Feature $feature = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

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

    public function getFeature(): ?Feature
    {
        return $this->feature;
    }

    public function setFeature(?Feature $feature): self
    {
        $this->feature = $feature;

        return $this;
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
}
