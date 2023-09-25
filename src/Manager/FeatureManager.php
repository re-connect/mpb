<?php

namespace App\Manager;

use App\Entity\Feature;
use App\Entity\Tag;
use App\Repository\FeatureRepository;
use App\Traits\UserAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class FeatureManager extends UserRequestManager
{
    use UserAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly FeatureRepository $repository,
    ) {
        parent::__construct($em, $security);
    }

    /** @return Feature[] */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function toggleTag(Feature $feature, Tag $tag): void
    {
        $feature->toggleTag($tag);
        $this->em->flush();
    }

    public function createFeature(): Feature
    {
        $feature = new Feature();
        $this->create($feature);

        return $feature;
    }
}
