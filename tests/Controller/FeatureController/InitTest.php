<?php

namespace App\Tests\Controller\FeatureController;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Proxy;

class InitTest extends AbstractControllerTest
{
    private const URL = '/features/init';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $expectedRoute, string $userEmail = null): void
    {
        self::ensureKernelShutdown();
        $clientTest = static::createClient();

        if ($userEmail) {
            /** @var Proxy<User> $user */
            $user = UserFactory::findOrCreate(['email' => $userEmail]);
            $clientTest->loginUser($user->object());
        }

        $clientTest->followRedirects();
        $clientTest->request('GET', $url);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
        $this->assertRouteSame($expectedRoute);
    }

    public function provideTestRoute(): \Generator
    {
        $expectedRoute = 'feature_new';
        yield 'Should redirect to login when not connected' => [self::URL, 200, 'app_login', null];
        yield 'Should redirect to creation when connected as user' => [self::URL, 200, $expectedRoute, UserFixtures::USER_MAIL];
        yield 'Should redirect to creation when connected as team member' => [self::URL, 200, $expectedRoute, UserFixtures::TEAM_USER_MAIL];
        yield 'Should redirect to creation when connected as tech team member' => [self::URL, 200, $expectedRoute, UserFixtures::TECH_TEAM_USER_MAIL];
        yield 'Should redirect to creation when connected as admin' => [self::URL, 200, $expectedRoute, UserFixtures::ADMIN_USER_MAIL];
    }
}
