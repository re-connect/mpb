<?php

namespace App\EventListener;

use App\Entity\Bug;
use App\Service\Notifier\AppNotifier;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;

#[AsEntityListener(event: 'preUpdate', entity: Bug::class)]
readonly class BugListener
{
    public function __construct(private AppNotifier $notificator)
    {
    }

    public function preUpdate(Bug $bug, PreUpdateEventArgs $event): void
    {
        if ($this->bugHasBeenPublished($event, $bug) || $this->bugIsDone($event, $bug)) {
            $this->notificator->notify($bug);
        }
    }

    private function bugHasBeenPublished(PreUpdateEventArgs $event, Bug $bug): bool
    {
        return $event->hasChangedField('draft') && $bug->isPublished();
    }

    private function bugIsDone(PreUpdateEventArgs $event, Bug $bug): bool
    {
        return $event->hasChangedField('done') && $bug->isDone();
    }
}
