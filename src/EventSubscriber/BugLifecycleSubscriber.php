<?php

namespace App\EventSubscriber;

use App\Entity\Bug;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class BugLifecycleSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.bug_lifecycle.transition.dismiss' => 'markDone',
            'workflow.bug_lifecycle.transition.solve' => 'markDone',
        ];
    }

    public function markDone(TransitionEvent $event): void
    {
        $bug = $event->getSubject();
        if (!$bug instanceof Bug) {
            return;
        }
        $bug->setDone(true);
    }
}
