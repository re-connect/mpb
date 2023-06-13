<?php

namespace App\Manager;

use App\Entity\Feature;
use App\Entity\Tag;
use App\Repository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;

class FeatureManager extends UserRequestManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FeatureRepository $repository,
    ) {
        parent::__construct($em);
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
