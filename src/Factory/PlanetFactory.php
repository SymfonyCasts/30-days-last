<?php

namespace App\Factory;

use App\Entity\Planet;
use App\Repository\PlanetRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Planet>
 *
 * @method        Planet|Proxy                     create(array|callable $attributes = [])
 * @method static Planet|Proxy                     createOne(array $attributes = [])
 * @method static Planet|Proxy                     find(object|array|mixed $criteria)
 * @method static Planet|Proxy                     findOrCreate(array $attributes)
 * @method static Planet|Proxy                     first(string $sortedField = 'id')
 * @method static Planet|Proxy                     last(string $sortedField = 'id')
 * @method static Planet|Proxy                     random(array $attributes = [])
 * @method static Planet|Proxy                     randomOrCreate(array $attributes = [])
 * @method static PlanetRepository|RepositoryProxy repository()
 * @method static Planet[]|Proxy[]                 all()
 * @method static Planet[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Planet[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Planet[]|Proxy[]                 findBy(array $attributes)
 * @method static Planet[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Planet[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class PlanetFactory extends ModelFactory
{
    public const PLANET_NAMES = [
        'Mercury',
        'Venus',
        'Earth',
        'Mars',
        'Jupiter',
        'Saturn',
        'Uranus',
        'Neptune',
    ];

    public const OTHER_PLANET_NAMES = [
        'Proxima Centauri b',
        'Kepler-186f',
        'Kepler-62e',
        'Kepler-62f',
    ];

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'description' => self::faker()->text(),
            'lightYearsFromEarth' => self::faker()->randomFloat(),
            'name' => self::faker()->randomElement(self::PLANET_NAMES),
            'imageFilename' => 'planet-'.self::faker()->numberBetween(1, 4).'.png',
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Planet $planet): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Planet::class;
    }
}
