<?php

namespace App\EventListener;

use App\Entity\Bug;
use App\Entity\Comment;
use App\Entity\Feature;
use App\Traits\UserAwareTrait;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

class TraceableEntityListener
{
    use UserAwareTrait;

    public function __construct(private readonly Security $security)
    {
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$this->supports($entity)) {
            return;
        }

        $entity->setUpdatedAt();
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$this->supports($entity)) {
            return;
        }

        $entity->setCreatedAt();
        if ($this->getUser()) {
            $entity->setUser($this->getUser());
        }
    }

    public function supports(object $entity): bool
    {
        return $entity instanceof Comment || $entity instanceof Bug || $entity instanceof Feature;
    }
}
