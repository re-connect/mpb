<?php

namespace App\Entity;

use App\Repository\FeatureRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeatureRepository::class)]
class Feature extends UserRequest
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'features')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Application $application = null;

    #[ORM\Column]
    private ?bool $done = false;

    /** @var Collection<int, Comment> $comments */
    #[ORM\OneToMany(mappedBy: 'feature', targetEntity: Comment::class)]
    private Collection $comments;

    /** @var Collection<int, Vote> */
    #[ORM\OneToMany(mappedBy: 'feature', targetEntity: Vote::class)]
    private Collection $votes;

    /** @var Collection<int, Tag> */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'features')]
    private Collection $tags;

    #[ORM\Column(length: 255)]
    private ?string $center = null;

    #[ORM\Column(type: 'string', enumType: FeatureStatus::class)]
    private ?FeatureStatus $status;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->status = FeatureStatus::ToBeDecided;
    }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function isDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function markDone(): self
    {
        return $this->setDone(true);
    }

    /** @return Collection<int, Comment> */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setFeature($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFeature() === $this) {
                $comment->setFeature(null);
            }
        }

        return $this;
    }

    public function hasComments(): bool
    {
        return $this->comments->count() > 0;
    }

    /** @return Collection<int, Vote> */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setFeature($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getFeature() === $this) {
                $vote->setFeature(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, Tag> */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addFeature($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeFeature($this);
        }

        return $this;
    }

    public function isTaggedWith(Tag $tag): bool
    {
        return $this->tags->contains($tag);
    }

    public function toggleTag(Tag $tag): void
    {
        if (!$this->isTaggedWith($tag)) {
            $this->addTag($tag);
        } else {
            $this->removeTag($tag);
        }
    }

    public function getCenter(): ?string
    {
        return $this->center;
    }

    public function setCenter(?string $center): self
    {
        $this->center = $center;

        return $this;
    }

    public function getStatus(): ?FeatureStatus
    {
        return $this->status;
    }

    public function setStatus(?FeatureStatus $status): void
    {
        $this->status = $status;
        if (in_array($this->status, FeatureStatus::FINAL_STATUS)) {
            $this->done = true;
        }
    }
}
