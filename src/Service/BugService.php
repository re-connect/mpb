<?php

namespace App\Service;

use App\Entity\UserRequest;
use App\Form\Model\UserRequestSearch;
use App\Repository\BugRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class BugService extends UserRequestService
{
    public function __construct(
        private readonly BugRepository $repository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly Security $security,
    ) {
        parent::__construct($this->authorizationChecker, $this->security);
    }

    /** @return UserRequest[] */
    public function getAccessible(UserRequestSearch $search): array
    {
        return $this->getAccessibleUserRequests($search, $this->repository);
    }
}
