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
        foreach (PlanetFactory::PLANET_NAMES as $name) {
            PlanetFactory::new([
                'name' => $name,
            ])->create();
        }

        VoyageFactory::createMany(30, [
            'planet' => PlanetFactory::random(),
        ]);

        $manager->flush();
    }
}
