<?php

namespace App\Entity;

use App\Traits\DraftableTrait;
use App\Traits\UploadableTrait;
use Doctrine\Common\Collections\Collection;

abstract class UserRequest
{
    use UploadableTrait;
    use DraftableTrait;

    abstract public function getId(): ?int;

    /** @return Collection<int, Vote> */
    abstract public function getVotes(): Collection;

    abstract public function addVote(Vote $vote): self;

    abstract public function removeVote(Vote $vote): self;

    abstract public function getUser(): ?User;

    abstract public function setUser(?User $user): self;

    abstract public function setDone(bool $done): self;

    abstract public function isDone(): ?bool;

    abstract public function resolve(): self;

    public function isBug(): bool
    {
        return false;
    }

    public function isFeature(): bool
    {
        return false;
    }
}
