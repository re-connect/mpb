<?php

namespace App\DataFixtures;

use App\EventListener\TraceableEntityListener;
use App\Tests\Factory\BugFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class BugFixtures extends Fixture implements DependentFixtureInterface
{
    use DisableEntityListener;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->disableEntityListener(TraceableEntityListener::class);
        BugFactory::createMany(3, ['assignee' => null]);
        BugFactory::createMany(3, ['assignee' => null, 'draft' => true]);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
