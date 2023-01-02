<?php

namespace App\Tests\Controller\FeatureController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;

class ListTest extends AbstractControllerTest implements TestRouteInterface
{
    private const URL = '/features/list';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, ?string $userEmail = null, ?string $expectedRedirect = null, string $method = 'GET'): void
    {
        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield 'Should redirect to login when not connected' => [self::URL, 302, null, 'http://localhost/login'];
        yield 'Should return 200 when connected as user' => [self::URL, 200, UserFixtures::USER_MAIL];
        yield 'Should return 200 when connected as team member' => [self::URL, 200, UserFixtures::TEAM_USER_MAIL];
        yield 'Should return 200 when connected as tech team member' => [self::URL, 200, UserFixtures::TECH_TEAM_USER_MAIL];
        yield 'Should return 200 when connected as admin' => [self::URL, 200, UserFixtures::ADMIN_USER_MAIL];
    }
}
