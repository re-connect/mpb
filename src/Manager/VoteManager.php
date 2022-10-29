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

    public function voteForItem(UserRequest $item): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }
        $currentVote = $user->getVoteForItem($item);

        if (!$currentVote) {
            $vote = (new Vote())->setItem($item)->setVoter($user);
            $this->em->persist($vote);
        } else {
            $this->em->remove($currentVote);
        }

        $this->em->flush();
    }
}
