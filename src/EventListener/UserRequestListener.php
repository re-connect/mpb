<?php

namespace App\EventListener;

use App\Entity\Bug;
use App\Entity\Feature;
use App\Entity\UserRequest;
use App\Service\Notifier\AppNotifier;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;

#[AsEntityListener(event: 'preUpdate', entity: Bug::class)]
#[AsEntityListener(event: 'preUpdate', entity: Feature::class)]
readonly class UserRequestListener
{
    public function __construct(private AppNotifier $notificator)
    {
    }

    public function preUpdate(UserRequest $request, PreUpdateEventArgs $event): void
    {
        if ($this->hasBeenPublished($event, $request) || $this->isDone($event, $request)) {
            $this->notificator->notify($request);
        }
    }

    private function hasBeenPublished(PreUpdateEventArgs $event, UserRequest $request): bool
    {
        return $event->hasChangedField('draft') && $request->isPublished();
    }

    private function isDone(PreUpdateEventArgs $event, UserRequest $request): bool
    {
        return $event->hasChangedField('done') && $request->isDone();
    }
}
