<?php

namespace App\Entity;

use App\Repository\BugReportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BugReportRepository::class)
 */
class BugReport
{
    const APPLICATIONS = [
        0 => 'Coffre-fort Numérique',
        1 => 'Reconnect Pro',
        2 => 'Application Mobile',
    ];
    const ENVIRONMENTS = [
        0 => 'Pre-production',
        1 => 'Production'
    ];
    const ACCOUNT_TYPE = [
        0 => 'CFN - Bénéficiaire',
        1 => 'CFN - Membre',
        2 => 'CFN - Gestionnaire',
        3 => 'CFN - Admin',
        4 => 'RP - Membre',
        5 => 'RP - Admin',
    ];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;
    /**
     * @ORM\Column(type="text")
     */
    private $content;
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;
    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bugReports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="bugReport")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;
    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="bugReport", orphanRemoval=true)
     */
    private $comments;
    /**
     * @ORM\OneToMany(targetEntity=Attachment::class, mappedBy="bugReport", orphanRemoval=true)
     */
    private $attachments;
    /**
     * @ORM\ManyToOne(targetEntity=Status::class, inversedBy="bugReport")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;
    /**
     * @ORM\Column(type="integer")
     */
    private $application;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $device;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $device_language;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $device_os;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $device_os_version;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $browser;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $browser_version;
    /**
     * @ORM\Column(type="text")
     */
    private $history;
    /**
     * @ORM\Column(type="integer")
     */
    private $environment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $account_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $account_type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $item_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $other_info;

    static public function getConstValues($array): array
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

    public function setContent(string $content): self
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Comment[]
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
     * @return Collection|Attachment[]
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

    public function getApplication(): ?int
    {
        return $this->application;
    }

    public function setApplication(int $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getDeviceLanguage(): ?string
    {
        return $this->device_language;
    }

    public function setDeviceLanguage(string $device_language): self
    {
        $this->device_language = $device_language;

        return $this;
    }

    public function getDeviceOs(): ?string
    {
        return $this->device_os;
    }

    public function setDeviceOs(string $device_os): self
    {
        $this->device_os = $device_os;

        return $this;
    }

    public function getDeviceOsVersion(): ?string
    {
        return $this->device_os_version;
    }

    public function setDeviceOsVersion(?string $device_os_version): self
    {
        $this->device_os_version = $device_os_version;

        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(string $browser): self
    {
        $this->browser = $browser;

        return $this;
    }

    public function getBrowserVersion(): ?string
    {
        return $this->browser_version;
    }

    public function setBrowserVersion(?string $browser_version): self
    {
        $this->browser_version = $browser_version;

        return $this;
    }

    public function getHistory(): ?string
    {
        return $this->history;
    }

    public function setHistory(string $history): self
    {
        $this->history = $history;

        return $this;
    }

    public function getEnvironment(): ?int
    {
        return $this->environment;
    }

    public function setEnvironment(int $environment): self
    {
        $this->environment = $environment;

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

    public function getOtherInfo(): ?string
    {
        return $this->other_info;
    }

    public function setOtherInfo(?string $other_info): self
    {
        $this->other_info = $other_info;

        return $this;
    }
}
