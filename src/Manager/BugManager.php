<?php

namespace App\Manager;

use App\Entity\Bug;
use App\Traits\UserAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BugManager extends UserRequestManager
{
    use UserAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
        parent::__construct($em, $security);
    }

    public function takeOver(Bug $bug): void
    {
        $bug->setAssignee($this->getUser());
        $this->em->flush();
    }

    public function createBug(string $userAgent): Bug
    {
        $bug = new Bug($userAgent);
        $this->create($bug);

        return $bug;
    }
}
