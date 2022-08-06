<?php

namespace App\Entity;

use App\Repository\UserKindRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserKindRepository::class)]
class UserKind extends StyledEntityKind
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Bug> $bugs
     */
    #[ORM\OneToMany(mappedBy: 'userKind', targetEntity: Bug::class)]
    private Collection $bugs;

    public function __construct()
    {
        $this->bugs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Bug>
     */
    public function getBugs(): Collection
    {
        return $this->bugs;
    }

    public function addBugReport(Bug $bug): self
    {
        if (!$this->bugs->contains($bug)) {
            $this->bugs[] = $bug;
            $bug->setUserKind($this);
        }

        return $this;
    }

    public function removeBugReport(Bug $bug): self
    {
        if ($this->bugs->removeElement($bug)) {
            // set the owning side to null (unless already changed)
            if ($bug->getUserKind() === $this) {
                $bug->setUserKind(null);
            }
        }

        return $this;
    }
}
