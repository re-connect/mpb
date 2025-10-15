<?php

namespace App\Service;

use App\Entity\Feature;
use App\Entity\UserRequest;
use App\Form\Model\UserRequestSearch;
use App\Repository\FeatureRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class FeatureService extends UserRequestService
{
    public function __construct(
        private readonly FeatureRepository $repository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly Security $security,
    ) {
        parent::__construct($this->authorizationChecker, $this->security);
    }

    /** @return Feature[] */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /** @return UserRequest[] */
    public function getAccessible(UserRequestSearch $search): array
    {
        return $this->getAccessibleUserRequests($search, $this->repository);
    }

    /**
     * @return array<array<string, string>>
     */
    public function getAllCentersForAutocomplete(): array
    {
        return array_map(fn (string $center) => ['value' => $center, 'text' => $center], $this->getAllCenters());
    }

    /**
     * @return array<string>
     */
    public function getAllCenters(): array
    {
        $centers = array_column($this->repository->getAllCenters(), 'center');
        $centers = array_merge_recursive(...array_map(fn ($center) => explode(',', (string) $center), $centers));

        return array_unique($centers);
    }

    /**
     * @return Feature[]
     */
    public function getDraftsToClean(): array
    {
        return $this->repository->findDraftsToClean();
    }
}
