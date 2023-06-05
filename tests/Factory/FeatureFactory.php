<?php

namespace App\Tests\Factory;

use App\Entity\Feature;
use App\Entity\FeatureStatus;
use App\Repository\FeatureRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Feature>
 *
 * @method        Feature|Proxy                     create(array|callable $attributes = [])
 * @method static Feature|Proxy                     createOne(array $attributes = [])
 * @method static Feature|Proxy                     find(object|array|mixed $criteria)
 * @method static Feature|Proxy                     findOrCreate(array $attributes)
 * @method static Feature|Proxy                     first(string $sortedField = 'id')
 * @method static Feature|Proxy                     last(string $sortedField = 'id')
 * @method static Feature|Proxy                     random(array $attributes = [])
 * @method static Feature|Proxy                     randomOrCreate(array $attributes = [])
 * @method static FeatureRepository|RepositoryProxy repository()
 * @method static Feature[]|Proxy[]                 all()
 * @method static Feature[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Feature[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Feature[]|Proxy[]                 findBy(array $attributes)
 * @method static Feature[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Feature[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class FeatureFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'application' => ApplicationFactory::randomOrCreate(),
            'center' => null,
            'content' => self::faker()->text(),
            'createdAt' => new \DateTimeImmutable(),
            'done' => false,
            'draft' => false,
            'status' => FeatureStatus::ToBeDecided,
            'title' => self::faker()->text(25),
            'user' => UserFactory::findOrCreateWithRole('ROLE_USER'),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return Feature::class;
    }
}
