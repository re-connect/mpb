<?php

namespace App\EventListener;

use App\Entity\Bug;
use App\Entity\Comment;
use App\Entity\Feature;
use App\Entity\UserRequest;
use App\Traits\UserAwareTrait;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Comment::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Feature::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Bug::class)]
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Comment::class)]
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Feature::class)]
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Bug::class)]
class TraceableEntityListener
{
    use UserAwareTrait;

    public function __construct(private readonly Security $security)
    {
    }

    public function preUpdate(UserRequest|Comment $entity, PreUpdateEventArgs $args): void
    {
        $entity->setUpdatedAt();
    }

    public function prePersist(UserRequest|Comment $entity, PrePersistEventArgs $args): void
    {
        $entity->setCreatedAt($entity->getCreatedAt() ? \DateTimeImmutable::createFromInterface($entity->getCreatedAt()) : new \DateTimeImmutable());

        if ($this->getUser() && !$entity->getUser()) {
            $entity->setUser($this->getUser());
        }
    }
}
