<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [
            'Pas encore pris en compte' => 'danger',
            'Pris en compte' => 'warning',
            'En cours de résolution' => 'info',
            'Résolu' => 'success',
        ];
        foreach ($statuses as $name => $color) {
            $status = (new Status())->setName($name)->setColor($color);
            $manager->persist($status);
        }

        $manager->flush();
    }
}
