<?php

namespace App\Controller;

use App\Entity\User;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected function getUser(): ?User
    {
        $user = parent::getUser();
        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}
