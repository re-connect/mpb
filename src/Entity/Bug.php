<?php

namespace App\Entity;

use App\Repository\BugReportRepository;
use App\Traits\OwnedTrait;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'bug_report')]
#[ORM\Entity(repositoryClass: BugReportRepository::class)]
class Bug
{
    use TimestampableTrait;
    use OwnedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = '';

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private ?string $content = '';

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(mappedBy: 'bug', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Attachment>
     */
    #[ORM\OneToMany(mappedBy: 'bug', targetEntity: Attachment::class, orphanRemoval: true)]
    private Collection $attachments;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $account_id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $item_id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'bugs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Application $application = null;

    #[ORM\ManyToOne(targetEntity: UserKind::class, inversedBy: 'bugs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?UserKind $userKind = null;

    #[ORM\Column(type: 'boolean')]
    private bool $done = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'getBugsAssignedToMe')]
    private ?User $assignee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $attachementName = null;

    #[ORM\Column(nullable: true)]
    private ?bool $draft = true;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
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

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setBug($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBug() === $this) {
                $comment->setBug(null);
            }
        }

        return $this;
    }

    public function hasComments(): bool
    {
        return $this->comments->count() > 0;
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
            $attachment->setBug($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getBug() === $this) {
                $attachment->setBug(null);
            }
        }

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->account_id;
    }

    public function setAccountId(int $account_id): self
    {
        $this->account_id = $account_id;

        return $this;
    }

    public function getItemId(): ?int
    {
        return $this->item_id;
    }

    public function setItemId(?int $item_id): self
    {
        $this->item_id = $item_id;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

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

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function setAssignee(?User $assignee): self
    {
        $this->assignee = $assignee;

        return $this;
    }

    public function getUserKind(): ?UserKind
    {
        return $this->userKind;
    }

    public function setUserKind(?UserKind $userKind): self
    {
        $this->userKind = $userKind;

        return $this;
    }

    public function getAttachementName(): ?string
    {
        return $this->attachementName;
    }

    public function setAttachementName(?string $attachementName): self
    {
        $this->attachementName = $attachementName;

        return $this;
    }

    public function isDraft(): ?bool
    {
        return $this->draft;
    }

    public function setDraft(bool $draft): self
    {
        $this->draft = $draft;

        return $this;
    }

    public function publish(): self
    {
        $this->draft = false;

        return $this;
    }
}
