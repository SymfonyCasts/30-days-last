<?php

namespace App\Factory;

use App\Entity\Voyage;
use App\Repository\VoyageRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Voyage>
 *
 * @method        Voyage|Proxy                     create(array|callable $attributes = [])
 * @method static Voyage|Proxy                     createOne(array $attributes = [])
 * @method static Voyage|Proxy                     find(object|array|mixed $criteria)
 * @method static Voyage|Proxy                     findOrCreate(array $attributes)
 * @method static Voyage|Proxy                     first(string $sortedField = 'id')
 * @method static Voyage|Proxy                     last(string $sortedField = 'id')
 * @method static Voyage|Proxy                     random(array $attributes = [])
 * @method static Voyage|Proxy                     randomOrCreate(array $attributes = [])
 * @method static VoyageRepository|RepositoryProxy repository()
 * @method static Voyage[]|Proxy[]                 all()
 * @method static Voyage[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Voyage[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Voyage[]|Proxy[]                 findBy(array $attributes)
 * @method static Voyage[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Voyage[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class VoyageFactory extends ModelFactory
{
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
            'leaveAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('+1 day', '+1 year')),
            'planet' => PlanetFactory::new(),
            'purpose' => self::faker()->text(80),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Voyage $voyage): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Voyage::class;
    }
}
