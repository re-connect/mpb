<?php

namespace App\Security\Voter;

use App\Entity\Bug;
use App\Entity\UserRequest;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRequestVoter extends Voter
{
    public function __construct(private readonly AuthorizationCheckerInterface $checker)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [Permissions::UPDATE, Permissions::READ]) && $subject instanceof UserRequest;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface || !$subject instanceof UserRequest) {
            return false;
        }

        if ($this->checker->isGranted('ROLE_TECH_TEAM')) {
            return true;
        }

        if ($user !== $subject->getUser()) {
            return false;
        }

        if (Permissions::READ === $attribute) {
            return true;
        } elseif (Permissions::UPDATE === $attribute && $subject instanceof Bug) {
            return $subject->isDraft();
        }

        return false;
    }
}
