<?php

namespace App\Entity;

use App\Traits\DraftableTrait;
use App\Traits\TimestampableTrait;
use App\Traits\UploadableTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;

abstract class UserRequest implements \Stringable
{
    use UploadableTrait;
    use DraftableTrait;
    use TimestampableTrait;

    abstract public function getId(): ?int;

    abstract public function getApplication(): ?Application;

    abstract public function setApplication(?Application $application);

    /** @return Collection<int, Vote> */
    abstract public function getVotes(): Collection;

    abstract public function addVote(Vote $vote);

    abstract public function removeVote(Vote $vote);

    abstract public function getVotersNamesAsString(): string;

    abstract public function getComments(): Collection;

    abstract public function addComment(Comment $comment);

    abstract public function removeComment(Comment $comment);

    abstract public function getTitle(): ?string;

    abstract public function setTitle(?string $title);

    abstract public function getContent(): ?string;

    abstract public function setContent(?string $content);

    abstract public function getUser(): ?User;

    abstract public function setUser(?User $user);

    abstract public function setDone(bool $done);

    abstract public function isDone(): ?bool;

    abstract public function resolve();

    public function isBug(): bool
    {
        return false;
    }

    public function isFeature(): bool
    {
        return false;
    }

    public function getAssignee(): ?User
    {
        return null;
    }

    /**
     * @return ReadableCollection<int, string>
     */
    public function getVotersEmail(): ReadableCollection
    {
        return $this->getVotes()
            ->map(fn (Vote $vote) => $vote->getVoter()->getEmail())
            ->filter(fn (?string $email) => null !== $email && '' !== $email);
    }
}
