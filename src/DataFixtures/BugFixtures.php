<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Tests\Factory\BugFactory;
use App\Tests\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BugFixtures extends Fixture implements DependentFixtureInterface
{
    final public const DRAFT_FROM_BASIC_USER = 'draft_from_basic_user';
    final public const BUG_FROM_BASIC_USER = 'bug_from_basic_user';
    final public const DRAFT_FROM_TEAM_USER = 'draft_from_team_user';
    final public const BUG_FROM_TEAM_USER = 'bug_from_team_user';

    public function load(ObjectManager $manager): void
    {
        BugFactory::createMany(3, ['assignee' => null]);
        BugFactory::createMany(3, ['assignee' => null, 'draft' => true, 'title' => '']);
        BugFactory::createOne(['draft' => true, 'title' => self::DRAFT_FROM_BASIC_USER]);
        BugFactory::createOne(['draft' => false, 'title' => self::BUG_FROM_BASIC_USER]);
        BugFactory::createOne(['draft' => true, 'title' => self::DRAFT_FROM_TEAM_USER, 'user' => UserFactory::createOne(['roles' => [User::ROLE_TEAM]])]);
        BugFactory::createOne(['draft' => false, 'title' => self::BUG_FROM_TEAM_USER, 'user' => UserFactory::createOne(['roles' => [User::ROLE_TEAM]])]);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
