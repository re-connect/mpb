<?php

namespace App\Service\Notifier;

use App\Entity\UserRequest;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.notifier')]
interface ChannelNotifierInterface
{
    public function notify(UserRequest $request): void;
}
