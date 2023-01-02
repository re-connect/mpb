<?php

namespace App\Tests\Factory;

use App\Entity\Bug;
use App\Repository\BugRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Bug>
 *
 * @method        Bug|Proxy                     create(array|callable $attributes = [])
 * @method static Bug|Proxy                     createOne(array $attributes = [])
 * @method static Bug|Proxy                     find(object|array|mixed $criteria)
 * @method static Bug|Proxy                     findOrCreate(array $attributes)
 * @method static Bug|Proxy                     first(string $sortedField = 'id')
 * @method static Bug|Proxy                     last(string $sortedField = 'id')
 * @method static Bug|Proxy                     random(array $attributes = [])
 * @method static Bug|Proxy                     randomOrCreate(array $attributes = [])
 * @method static BugRepository|RepositoryProxy repository()
 * @method static Bug[]|Proxy[]                 all()
 * @method static Bug[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Bug[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static Bug[]|Proxy[]                 findBy(array $attributes)
 * @method static Bug[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Bug[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class BugFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'content' => self::faker()->text(),
            'createdAt' => new \DateTimeImmutable(),
            'done' => false,
            'draft' => false,
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
        return Bug::class;
    }
}
