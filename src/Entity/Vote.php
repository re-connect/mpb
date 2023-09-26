<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    private ?Bug $bug = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    private ?Feature $feature = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $voter = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setItem(UserRequest $request): self
    {
        if ($request->isFeature()) {
            /** @var Feature $request */
            $this->feature = $request;
        } elseif ($request->isBug()) {
            /** @var Bug $request */
            $this->bug = $request;
        }

        return $this;
    }

    public function getVoter(): ?User
    {
        return $this->voter;
    }

    public function setVoter(?User $voter): self
    {
        $this->voter = $voter;

        return $this;
    }
}
