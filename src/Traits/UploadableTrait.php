<?php

namespace App\Traits;

use App\Entity\Attachment;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

trait UploadableTrait
{
    #[Assert\Valid]
    protected Collection $attachments;

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): static
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setUserRequest($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): static
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getFeature() === $this) {
                $attachment->setUserRequest(null);
            }
        }

        return $this;
    }
}
