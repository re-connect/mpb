<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserRequest;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserRequestVoter extends Voter
{
    public function __construct(private readonly AuthorizationCheckerInterface $checker)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [Permissions::UPDATE, Permissions::READ, Permissions::DELETE]) && $subject instanceof UserRequest;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof User || !$subject instanceof UserRequest) {
            return false;
        }

        if ($this->checker->isGranted('ROLE_TECH_TEAM')) {
            return true;
        }

        return match ($attribute) {
            Permissions::READ => $this->canRead($subject, $user),
            Permissions::UPDATE => $this->canUpdate($subject, $user),
            Permissions::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    private function canRead(UserRequest $subject, User $user): bool
    {
        return $this->checker->isGranted('ROLE_TEAM') || $user === $subject->getUser();
    }

    private function canUpdate(UserRequest $subject, User $user): bool
    {
        return $subject->isDraft() && $user === $subject->getUser();
    }

    private function canDelete(UserRequest $subject, User $user): bool
    {
        return $user === $subject->getUser();
    }
}
