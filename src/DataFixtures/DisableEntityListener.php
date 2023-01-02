<?php

namespace App\DataFixtures;

use Doctrine\ORM\EntityManagerInterface;

trait DisableEntityListener
{
    private readonly EntityManagerInterface $em;

    public function disableEntityListener(string $listenerName): void
    {
        $eventManager = $this->em->getEventManager();
        $listeners = $eventManager->getAllListeners();
        foreach ($listeners as $type => $listenerByType) {
            if ('prePersist' === $type) {
                foreach ($listenerByType  as $name => $listener) {
                    $name = str_replace('_service_', '', (string) $name);
                    if ($name === $listenerName) {
                        /* @phpstan-ignore-next-line */
                        $eventManager->removeEventListener($type, $listenerName);
                    }
                }
            }
        }
    }
}
