<?php

namespace App\DataFixtures;

use App\Tests\Factory\FeatureFactory;
use App\Tests\Factory\VoteFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        VoteFactory::createOne(['feature' => FeatureFactory::find(['title' => FeatureFixtures::DONE_WITH_VOTE_FROM_TEAM_USER])]);
    }

    public function getDependencies(): array
    {
        return [
            FeatureFixtures::class,
        ];
    }
}
