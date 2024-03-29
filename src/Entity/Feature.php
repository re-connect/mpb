<?php

namespace App\Entity;

use App\Repository\FeatureRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FeatureRepository::class)]
class Feature extends UserRequest implements ExportableEntityInterface
{
    use TimestampableTrait;
    final public const EXPORTABLE_FIELDS = ['id', 'application', 'title', 'categories', 'description', 'user', 'requester', 'status', 'votes', 'center', 'creation_date'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'features')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotNull]
    private ?Application $application = null;

    #[ORM\Column]
    private ?bool $done = false;

    /** @var Collection<int, Comment> $comments */
    #[ORM\OneToMany(mappedBy: 'feature', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /** @var Collection<int, Vote> */
    #[ORM\OneToMany(mappedBy: 'feature', targetEntity: Vote::class, orphanRemoval: true)]
    private Collection $votes;

    /** @var Collection<int, Tag> */
    #[ORM\JoinTable(name: 'tag_feature')]
    #[ORM\JoinColumn(name: 'feature_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    private Collection $tags;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $center = null;

    #[ORM\Column(type: 'string', nullable: true, enumType: FeatureStatus::class, options: ['default' => FeatureStatus::ToBeDecided])]
    private ?FeatureStatus $status;

    /** @var Collection<int, Attachment> */
    #[ORM\OneToMany(mappedBy: 'feature', targetEntity: Attachment::class, orphanRemoval: true)]
    protected Collection $attachments;

    #[ORM\ManyToOne(targetEntity: Requester::class, inversedBy: 'features')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Requester $requestedBy = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->status = FeatureStatus::ToBeDecided;
        $this->attachments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): static
    {
        $this->application = $application;

        return $this;
    }

    public function isDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): static
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

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setFeature($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
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

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setFeature($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getFeature() === $this) {
                $vote->setFeature(null);
            }
        }

        return $this;
    }

    public function getVotersNamesAsString(): string
    {
        return implode(', ', array_map(fn (Vote $vote) => ucwords($vote->getVoter()?->getFullName() ?? ''), $this->votes->toArray()));
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
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

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

    /** @return array<int, string|null> */
    public function getTagNames(): array
    {
        return $this->tags->map(fn (Tag $tag) => $tag->getName())->toArray();
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

    public function setStatus(?FeatureStatus $status): self
    {
        $this->status = $status;
        if (in_array($this->status, FeatureStatus::FINAL_STATUS)) {
            $this->done = true;
        }

        return $this;
    }

    /** @return array<int, string|null> */
    public function getExportableData(): array
    {
        return [
            (string) $this->id,
            $this->application?->getName(),
            $this->title,
            implode(', ', $this->getTagNames()),
            strip_tags($this->content ?? ''),
            $this->user?->getEmail(),
            $this->requestedBy?->getName(),
            $this->status?->value,
            (string) count($this->votes),
            $this->center,
            $this->createdAt?->format('d/m/Y'),
        ];
    }

    public function resolve(): static
    {
        $this->setDone(true);

        return $this;
    }

    public function isFeature(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getRequestedBy(): ?Requester
    {
        return $this->requestedBy;
    }

    public function setRequestedBy(?Requester $requestedBy): static
    {
        $this->requestedBy = $requestedBy;

        return $this;
    }
}
