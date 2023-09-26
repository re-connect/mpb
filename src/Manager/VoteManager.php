<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\UserRequest;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class VoteManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function vote(UserRequest $request): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }
        $currentVote = $user->getVote($request);

        if (!$currentVote) {
            $vote = (new Vote())->setItem($request)->setVoter($user);
            $this->em->persist($vote);
        } else {
            $this->em->remove($currentVote);
        }

        $this->em->flush();
    }
}
