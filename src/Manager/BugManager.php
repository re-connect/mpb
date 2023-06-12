<?php

namespace App\Manager;

use App\Entity\Bug;
use App\Traits\UserAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BugManager
{
    use UserAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function takeOver(Bug $bug): void
    {
        $bug->setAssignee($this->getUser());
        $this->em->flush();
    }
}
