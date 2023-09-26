<?php

namespace App\Entity;

use App\Repository\AttachmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
#[Assert\Expression(
    'this.getUserRequest() !== null',
    message: 'user_request_not_null',
)]
class Attachment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\Column(type: 'integer')]
    private int $size = 0;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $uploadedBy = null;

    #[ORM\ManyToOne(targetEntity: Bug::class, inversedBy: 'attachments')]
    private ?Bug $bug = null;

    #[ORM\ManyToOne(targetEntity: Feature::class, inversedBy: 'attachments')]
    private ?Feature $feature = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(?User $uploadedBy): self
    {
        $this->uploadedBy = $uploadedBy;

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

    public function getUserRequest(): ?UserRequest
    {
        return $this->feature ?? $this->bug;
    }

    public function setUserRequest(?UserRequest $request): self
    {
        if ($request?->isFeature()) {
            /** @var Feature $request */
            $this->feature = $request;
        } elseif ($request?->isBug()) {
            /** @var Bug $request */
            $this->bug = $request;
        }

        return $this;
    }
}
