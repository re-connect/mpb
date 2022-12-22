<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    final public const ROLE_USER = 'ROLE_USER';
    final public const ROLE_TECH_TEAM = 'ROLE_TECH_TEAM';
    final public const ROLE_TEAM = 'ROLE_TEAM';
    final public const ROLE_ADMIN = 'ROLE_ADMIN';
    final public const ROLES = [
        self::ROLE_USER,
        self::ROLE_TECH_TEAM,
        self::ROLE_TEAM,
        self::ROLE_ADMIN,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $firstName = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $lastName = '';

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastLogin = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password = '';

    private ?string $plainPassword = null;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /** @var Collection<int, Bug> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Bug::class, orphanRemoval: true)]
    private Collection $bugs;

    /** @var Collection<int, Feature> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Feature::class, orphanRemoval: true)]
    private Collection $features;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Preference::class, cascade: ['persist', 'remove'])]
    private Preference $preference;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /** @var Collection<int, Badge> */
    #[ORM\ManyToMany(targetEntity: Badge::class, mappedBy: 'users')]
    private Collection $badges;

    /** @var Collection<int, Attachment> */
    #[ORM\OneToMany(mappedBy: 'uploadedBy', targetEntity: Attachment::class, orphanRemoval: true)]
    private Collection $attachments;

    /** @var Collection<int, Bug> */
    #[ORM\OneToMany(mappedBy: 'assignee', targetEntity: Bug::class)]
    private Collection $getBugsAssignedToMe;

    /** @var Collection<int, Vote> */
    #[ORM\OneToMany(mappedBy: 'voter', targetEntity: Vote::class, orphanRemoval: true)]
    private Collection $votes;

    public function __construct()
    {
        $this->bugs = new ArrayCollection();
        $this->features = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->badges = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->getBugsAssignedToMe = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getUserIdentifier();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /** @return Collection<int, Bug> */
    public function getBugs(): Collection
    {
        return $this->bugs;
    }

    public function addBug(Bug $bug): self
    {
        if (!$this->bugs->contains($bug)) {
            $this->bugs[] = $bug;
            $bug->setUser($this);
        }

        return $this;
    }

    public function removeBug(Bug $bug): self
    {
        if ($this->bugs->removeElement($bug)) {
            // set the owning side to null (unless already changed)
            if ($bug->getUser() === $this) {
                $bug->setUser(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, Feature> */
    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function addFeature(Feature $feature): self
    {
        if (!$this->features->contains($feature)) {
            $this->features[] = $feature;
            $feature->setUser($this);
        }

        return $this;
    }

    public function removeFeature(Feature $feature): self
    {
        if ($this->features->removeElement($feature)) {
            // set the owning side to null (unless already changed)
            if ($feature->getUser() === $this) {
                $feature->setUser(null);
            }
        }

        return $this;
    }

    public function getPreference(): ?Preference
    {
        return $this->preference;
    }

    public function setPreference(Preference $preference): self
    {
        // set the owning side of the relation if necessary
        if ($preference->getUser() !== $this) {
            $preference->setUser($this);
        }
        $this->preference = $preference;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Badge>
     */
    public function getBadges(): Collection
    {
        return $this->badges;
    }

    public function addBadge(Badge $badge): self
    {
        if (!$this->badges->contains($badge)) {
            $this->badges[] = $badge;
            $badge->addUser($this);
        }

        return $this;
    }

    public function removeBadge(Badge $badge): self
    {
        if ($this->badges->removeElement($badge)) {
            $badge->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
            $attachment->setUploadedBy($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getUploadedBy() === $this) {
                $attachment->setUploadedBy(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        $this->roles = array_unique([...$this->roles, $role]);

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return Collection<int, Bug>
     */
    public function getGetBugsAssignedToMe(): Collection
    {
        return $this->getBugsAssignedToMe;
    }

    public function addGetBugsAssignedToMe(Bug $getBugsAssignedToMe): self
    {
        if (!$this->getBugsAssignedToMe->contains($getBugsAssignedToMe)) {
            $this->getBugsAssignedToMe[] = $getBugsAssignedToMe;
            $getBugsAssignedToMe->setAssignee($this);
        }

        return $this;
    }

    public function removeGetBugsAssignedToMe(Bug $getBugsAssignedToMe): self
    {
        if ($this->getBugsAssignedToMe->removeElement($getBugsAssignedToMe)) {
            // set the owning side to null (unless already changed)
            if ($getBugsAssignedToMe->getAssignee() === $this) {
                $getBugsAssignedToMe->setAssignee(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, Vote> */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setVoter($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getVoter() === $this) {
                $vote->setVoter(null);
            }
        }

        return $this;
    }

    public function hasVotedBug(Bug $bug): bool
    {
        return $this->votes->exists(fn (int $key, Vote $vote) => $vote->getBug() === $bug);
    }

    public function getVoteForBug(Bug $bug): ?Vote
    {
        $vote = $this->votes->filter(fn (Vote $vote) => $vote->getBug() === $bug)->first();

        return false === $vote ? null : $vote;
    }

    public function getVoteForFeature(Feature $feature): ?Vote
    {
        $vote = $this->votes->filter(fn (Vote $vote) => $vote->getFeature() === $feature)->first();

        return false === $vote ? null : $vote;
    }

    public function getVoteForItem(UserRequest $item): ?Vote
    {
        if ($item instanceof Feature) {
            return $this->getVoteForFeature($item);
        } elseif ($item instanceof Bug) {
            return $this->getVoteForBug($item);
        }

        return null;
    }
}
