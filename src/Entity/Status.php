<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = '';

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $color = '';

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: BugReport::class)]
    private Collection $bugReports;

    public function __construct()
    {
        $this->bugReports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, BugReport>
     */
    public function getBugReports(): Collection
    {
        return $this->bugReports;
    }

    public function addBugReport(BugReport $bugReport): self
    {
        if (!$this->bugReports->contains($bugReport)) {
            $this->bugReports[] = $bugReport;
            $bugReport->setStatus($this);
        }

        return $this;
    }

    public function removeBugReport(BugReport $bugReport): self
    {
        if ($this->bugReports->removeElement($bugReport)) {
            // set the owning side to null (unless already changed)
            if ($bugReport->getStatus() === $this) {
                $bugReport->setStatus(null);
            }
        }

        return $this;
    }
}
