<?php

namespace App\Form\Model;

class UserRequestSearch
{
    public function __construct(
        private ?string $text = null,
        private ?bool $showDone = null,
        private ?int $application = null
    ) {
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getApplication(): ?int
    {
        return $this->application;
    }

    public function setApplication(?int $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getShowDone(): ?bool
    {
        return $this->showDone;
    }

    public function setShowDone(?bool $showDone): self
    {
        $this->showDone = $showDone;

        return $this;
    }
}
