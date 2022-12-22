<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Tests\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['roles' => [User::ROLE_USER]]);
        UserFactory::createOne(['roles' => [User::ROLE_TEAM]]);
    }
}
