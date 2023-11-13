<?php

namespace App\DataFixtures;

use App\Factory\PlanetFactory;
use App\Factory\VoyageFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        PlanetFactory::createMany(5);

        VoyageFactory::createMany(30, function () {
            return [
                'planet' => PlanetFactory::random(),
            ];
        });

        $manager->flush();
    }
}
