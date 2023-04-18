<?php

namespace App\Entity;

use App\Traits\DraftableTrait;
use App\Traits\UploadableTrait;
use Doctrine\Common\Collections\Collection;

abstract class UserRequest
{
    use UploadableTrait;
    use DraftableTrait;

    /** @return Collection<int, Vote> */
    abstract public function getVotes(): Collection;

    abstract public function addVote(Vote $vote): self;

    abstract public function removeVote(Vote $vote): self;

    abstract public function getUser(): ?User;

    abstract public function setUser(?User $user): self;
}
