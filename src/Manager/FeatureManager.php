<?php

namespace App\Manager;

use App\Entity\Feature;
use App\Entity\Tag;
use App\Repository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;

class FeatureManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FeatureRepository $repository,
    ) {
    }

    public function create(Feature $feature): void
    {
        $this->em->persist($feature);
        $this->em->flush();
    }

    /** @return Feature[] */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function markDone(Feature $feature): void
    {
        $feature->markDone();
        $this->em->flush();
    }

    public function toggleTag(Feature $feature, Tag $tag): void
    {
        $feature->toggleTag($tag);
        $this->em->flush();
    }
}
