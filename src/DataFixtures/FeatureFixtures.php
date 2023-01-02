<?php

namespace App\DataFixtures;

use App\EventListener\TraceableEntityListener;
use App\Tests\Factory\FeatureFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class FeatureFixtures extends Fixture implements DependentFixtureInterface
{
    use DisableEntityListener;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->disableEntityListener(TraceableEntityListener::class);
        FeatureFactory::createMany(3);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
