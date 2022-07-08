<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @return \App\Entity\User|null
     */
    protected function getUser(): ?UserInterface
    {
        return parent::getUser();
    }
}
