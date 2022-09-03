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
     * @var Collection<int, Bug> $bugs
     */
    #[ORM\OneToMany(mappedBy: 'application', targetEntity: Bug::class)]
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

    public function addBug(Bug $bug): self
    {
        if (!$this->bugs->contains($bug)) {
            $this->bugs[] = $bug;
            $bug->setApplication($this);
        }

        return $this;
    }

    public function removeBug(Bug $bug): self
    {
        if ($this->bugs->removeElement($bug)) {
            // set the owning side to null (unless already changed)
            if ($bug->getApplication() === $this) {
                $bug->setApplication(null);
            }
        }

        return $this;
    }
}
