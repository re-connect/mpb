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
     * @var Collection<int, Bug> $bugReports
     */
    #[ORM\OneToMany(mappedBy: 'userKind', targetEntity: Bug::class)]
    private Collection $bugReports;

    public function __construct()
    {
        $this->bugReports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Bug>
     */
    public function getBugReports(): Collection
    {
        return $this->bugReports;
    }

    public function addBugReport(Bug $bugReport): self
    {
        if (!$this->bugReports->contains($bugReport)) {
            $this->bugReports[] = $bugReport;
            $bugReport->setUserKind($this);
        }

        return $this;
    }

    public function removeBugReport(Bug $bugReport): self
    {
        if ($this->bugReports->removeElement($bugReport)) {
            // set the owning side to null (unless already changed)
            if ($bugReport->getUserKind() === $this) {
                $bugReport->setUserKind(null);
            }
        }

        return $this;
    }
}
