<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Tests\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    final public const USER_MAIL = 'user@mail.com';
    final public const TEAM_USER_MAIL = 'team@mail.com';
    final public const TECH_TEAM_USER_MAIL = 'tech_team@mail.com';
    final public const ADMIN_USER_MAIL = 'admin@mail.com';

    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['email' => self::USER_MAIL, 'roles' => [User::ROLE_USER]]);
        UserFactory::createOne(['email' => self::TEAM_USER_MAIL, 'roles' => [User::ROLE_TEAM]]);
        UserFactory::createOne(['email' => self::TECH_TEAM_USER_MAIL, 'roles' => [User::ROLE_TECH_TEAM]]);
        UserFactory::createOne(['email' => self::ADMIN_USER_MAIL, 'roles' => [User::ROLE_ADMIN]]);
    }
}
