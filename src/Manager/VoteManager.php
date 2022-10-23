<?php

namespace App\Manager;

use App\Entity\Bug;
use App\Entity\Feature;
use App\Entity\User;
use App\Entity\UserRequest;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class VoteManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    public function unVoteForItem(Bug|Feature $feature): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if ($user && $vote = $user->getVoteForItem($feature)) {
            $this->em->remove($vote);
            $this->em->flush();
        }
    }

    public function voteForItem(UserRequest $item): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if ($user && !$user->getVoteForItem($item)) {
            $vote = (new Vote())->setItem($item)->setVoter($user);
            $this->em->persist($vote);
            $this->em->flush();
        }
    }
}
