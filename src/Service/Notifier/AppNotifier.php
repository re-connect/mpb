<?php

namespace App\Service\Notifier;

use App\Entity\UserRequest;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

readonly class AppNotifier
{
    public function __construct(
        /** @var iterable<ChannelNotifierInterface> */
        #[TaggedIterator('app.notifier')] private iterable $notifiers,
        private string $env,
    ) {
    }

    public function notify(UserRequest $userRequest): void
    {
        if ('prod' === $this->env) {
            foreach ($this->notifiers as $notifier) {
                $notifier->notify($userRequest);
            }
        }
    }
}
