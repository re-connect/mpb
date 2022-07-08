<?php

namespace App\Entity;

use App\Repository\PreferenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreferenceRepository::class)]
class Preference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'preference', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'boolean')]
    private bool $hasAcceptedSlack = false;

    #[ORM\Column(type: 'boolean')]
    private bool $hasAcceptedEmail = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getHasAcceptedSlack(): ?bool
    {
        return $this->hasAcceptedSlack;
    }

    public function setHasAcceptedSlack(bool $hasAcceptedSlack): self
    {
        $this->hasAcceptedSlack = $hasAcceptedSlack;

        return $this;
    }

    public function getHasAcceptedEmail(): ?bool
    {
        return $this->hasAcceptedEmail;
    }

    public function setHasAcceptedEmail(bool $hasAcceptedEmail): self
    {
        $this->hasAcceptedEmail = $hasAcceptedEmail;

        return $this;
    }
}
