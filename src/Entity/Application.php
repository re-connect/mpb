<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application extends StyledEntityKind
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * @var Collection<int, Bug> $bugReports
     */
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: Bug::class)]
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
    public function getBugs(): Collection
    {
        return $this->bugReports;
    }

    public function addBugReport(Bug $bugReport): self
    {
        if (!$this->bugReports->contains($bugReport)) {
            $this->bugReports[] = $bugReport;
            $bugReport->setApplication($this);
        }

        return $this;
    }

    public function removeBugReport(Bug $bugReport): self
    {
        if ($this->bugReports->removeElement($bugReport)) {
            // set the owning side to null (unless already changed)
            if ($bugReport->getApplication() === $this) {
                $bugReport->setApplication(null);
            }
        }

        return $this;
    }
}
