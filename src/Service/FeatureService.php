<?php

namespace App\Service;

use App\Entity\Feature;
use Doctrine\ORM\EntityManagerInterface;

class FeatureService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function create(Feature $feature): void
    {
        $this->em->persist($feature);
        $this->em->flush();
    }
}
