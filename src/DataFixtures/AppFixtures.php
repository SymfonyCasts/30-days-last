<?php

namespace App\DataFixtures;

use App\Entity\Planet;
use App\Factory\PlanetFactory;
use App\Factory\VoyageFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        PlanetFactory::createMany(5);
        PlanetFactory::createMany(2, function() {
            $names = PlanetFactory::OTHER_PLANET_NAMES;

            return [
                'isInMilkyWay' => false,
                'name' => $names[array_rand($names)]
            ];
        });

        VoyageFactory::createMany(30, function () {
            return [
                'planet' => PlanetFactory::random(),
            ];
        });

        $manager->flush();
    }
}
