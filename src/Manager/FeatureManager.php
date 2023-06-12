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
}
