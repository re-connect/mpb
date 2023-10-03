<?php

namespace App\Tests\Factory;

use App\Entity\Requester;
use App\Repository\RequesterRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Requester>
 *
 * @method        Requester|Proxy                     create(array|callable $attributes = [])
 * @method static Requester|Proxy                     createOne(array $attributes = [])
 * @method static Requester|Proxy                     find(object|array|mixed $criteria)
 * @method static Requester|Proxy                     findOrCreate(array $attributes)
 * @method static Requester|Proxy                     first(string $sortedField = 'id')
 * @method static Requester|Proxy                     last(string $sortedField = 'id')
 * @method static Requester|Proxy                     random(array $attributes = [])
 * @method static Requester|Proxy                     randomOrCreate(array $attributes = [])
 * @method static RequesterRepository|RepositoryProxy repository()
 * @method static Requester[]|Proxy[]                 all()
 * @method static Requester[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Requester[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Requester[]|Proxy[]                 findBy(array $attributes)
 * @method static Requester[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Requester[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class RequesterFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->text(20),
        ];
    }

    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Requester $requester): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Requester::class;
    }
}
