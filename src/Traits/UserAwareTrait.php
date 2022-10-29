<?php

namespace App\Traits;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

trait UserAwareTrait
{
    private readonly Security $security;

    protected function getUser(): ?User
    {
        $user = $this->security->getUser();

        return $user instanceof User ? $user : null;
    }
}
