<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->addRole('ROLE_USER')
            ->setEmail('tester@gmail.com');
        $manager->persist($user);
        $userIntern = (new User())
            ->addRole('ROLE_TEAM')
            ->setEmail('tester_team@gmail.com');
        $manager->persist($userIntern);

        $manager->flush();
    }
}
