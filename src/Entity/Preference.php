<?php

namespace App\Entity;

use App\Repository\PreferenceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PreferenceRepository::class)
 */
class Preference
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="preference", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    /**
     * @ORM\Column(type="boolean")
     */
    private $hasAcceptedSlack;
    /**
     * @ORM\Column(type="boolean")
     */
    private $hasAcceptedEmail;

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
