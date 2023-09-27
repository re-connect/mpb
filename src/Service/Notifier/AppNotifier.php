<?php

namespace App\Service\Notifier;

use App\Entity\UserRequest;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

readonly class AppNotifier
{
    public function __construct(
        /** @var iterable<ChannelNotifierInterface> */
        #[TaggedIterator('app.notifier')] private iterable $notifiers
    ) {
    }

    public function notify(UserRequest $userRequest): void
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->notify($userRequest);
        }
    }
}
