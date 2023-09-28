<?php

namespace App\Tests\Factory;

use App\Entity\Vote;
use App\Repository\VoteRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Vote>
 *
 * @method        Vote|Proxy                     create(array|callable $attributes = [])
 * @method static Vote|Proxy                     createOne(array $attributes = [])
 * @method static Vote|Proxy                     find(object|array|mixed $criteria)
 * @method static Vote|Proxy                     findOrCreate(array $attributes)
 * @method static Vote|Proxy                     first(string $sortedField = 'id')
 * @method static Vote|Proxy                     last(string $sortedField = 'id')
 * @method static Vote|Proxy                     random(array $attributes = [])
 * @method static Vote|Proxy                     randomOrCreate(array $attributes = [])
 * @method static VoteRepository|RepositoryProxy repository()
 * @method static Vote[]|Proxy[]                 all()
 * @method static Vote[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Vote[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Vote[]|Proxy[]                 findBy(array $attributes)
 * @method static Vote[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Vote[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class VoteFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'voter' => UserFactory::new(),
            'feature' => FeatureFactory::new(),
            'bug' => null,
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Vote $vote): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Vote::class;
    }
}
