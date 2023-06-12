<?php

namespace App\EventListener;

use App\Entity\Bug;
use App\Service\NotificationService;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class BugListener
{
    public function __construct(private readonly NotificationService $notificator,
    ) {
    }

    public function preUpdate(Bug $bug, PreUpdateEventArgs $event): void
    {
        if ($this->bugHasBeenPublished($event, $bug) || $this->bugIsDone($event, $bug)) {
            $this->notificator->notifyBug($bug);
        }
    }

    private function bugHasBeenPublished(PreUpdateEventArgs $event, Bug $bug): bool
    {
        return $event->hasChangedField('draft') && !$bug->isDraft();
    }

    private function bugIsDone(PreUpdateEventArgs $event, Bug $bug): bool
    {
        return $event->hasChangedField('done') && $bug->isDone();
    }
}
