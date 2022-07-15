<?php

namespace App\Entity;

use App\Repository\BugReportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BugReportRepository::class)]
class BugReport
{
    final public const ACCOUNT_TYPE = [
        0 => 'Bénéficiaire',
        1 => 'TS',
        2 => 'Gestionnaire',
        3 => 'Admin',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = '';

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private ?string $content = '';

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bugReports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(mappedBy: 'bugReport', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Attachment>
     */
    #[ORM\OneToMany(mappedBy: 'bugReport', targetEntity: Attachment::class, orphanRemoval: true)]
    private Collection $attachments;

    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'bugReport')]
    private ?Status $status = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $account_id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $account_type = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $item_id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $user_in_charge = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'bugReports')]
    private ?Application $application = null;

    #[ORM\Column(type: 'boolean')]
    private bool $done = false;

    /**
     * @param string[] $array
     *
     * @return string[]
     */
    public static function getConstValues(array $array): array
    {
        $output = [];
        foreach ($array as $key => $value) {
            $output[$value] = $key;
        }

        return $output;
    }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
            $comment->setBugReport($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBugReport() === $this) {
                $comment->setBugReport(null);
            }
        }

        return $this;
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
            $attachment->setBugReport($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getBugReport() === $this) {
                $attachment->setBugReport(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

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

    public function getAccountType(): ?int
    {
        return $this->account_type;
    }

    public function setAccountType(int $account_type): self
    {
        $this->account_type = $account_type;

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

    public function getUserInCharge(): ?string
    {
        return $this->user_in_charge;
    }

    public function setUserInCharge(?string $user_in_charge): self
    {
        $this->user_in_charge = $user_in_charge;

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
}
