<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\EventListener\TraceableEntityListener;
use App\Tests\Factory\BugFactory;
use App\Tests\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class BugFixtures extends Fixture implements DependentFixtureInterface
{
    use DisableEntityListener;
    public const BUG_FROM_BASIC_USER = 'bug_from_basic_user';
    public const BUG_FROM_TEAM_USER = 'bug_from_team_user';

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->disableEntityListener(TraceableEntityListener::class);
        BugFactory::createMany(3, ['assignee' => null]);
        BugFactory::createMany(3, ['assignee' => null, 'draft' => true, 'title' => '']);
        BugFactory::createOne(['title' => self::BUG_FROM_BASIC_USER]);
        BugFactory::createOne(['title' => self::BUG_FROM_TEAM_USER, 'user' => UserFactory::createOne(['roles' => [User::ROLE_TEAM]])]);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
