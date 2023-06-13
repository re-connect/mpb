<?php

namespace App\Manager;

use App\Entity\UserRequest;
use App\Traits\UserAwareTrait;
use Doctrine\ORM\EntityManagerInterface;

class UserRequestManager
{
    use UserAwareTrait;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function publishDraft(UserRequest $userRequest): void
    {
        $userRequest->publish();
        $this->em->flush();
    }

    public function remove(UserRequest $userRequest): void
    {
        $this->em->remove($userRequest);
        $this->em->flush();
    }

    public function create(UserRequest $userRequest): void
    {
        $this->em->persist($userRequest);
        $this->em->flush();
    }

    public function markDone(UserRequest $userRequest): void
    {
        $userRequest->resolve();
        $this->em->flush();
    }
}
