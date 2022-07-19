<?php

namespace App\Security\Voter;

use App\Entity\BugReport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BugVoter extends Voter
{
    public function __construct(private readonly AuthorizationCheckerInterface $checker)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return Permissions::MANAGE === $attribute && $subject instanceof BugReport;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface || !$subject instanceof BugReport) {
            return false;
        }

        if ($this->checker->isGranted('ROLE_TEAM')) {
            return true;
        }

        if (Permissions::MANAGE === $attribute) {
            return $user === $subject->getUser();
        }

        return false;
    }
}
