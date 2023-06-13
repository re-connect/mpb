<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait DraftableTrait
{
    #[ORM\Column(nullable: false, options: ['default' => true])]
    protected ?bool $draft = true;

    public function isDraft(): bool
    {
        return (bool) $this->draft;
    }

    public function setDraft(bool $draft): static
    {
        $this->draft = $draft;

        return $this;
    }

    public function publish(): static
    {
        $this->draft = false;

        return $this;
    }

    public function isPublished(): bool
    {
        return !$this->draft;
    }
}
