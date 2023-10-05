<?php

namespace App\Tests\Controller\BugController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\BugFactory;

class TakeOverTest extends AbstractControllerTest implements TestRouteInterface
{
    private const TAKE_OVER_URL = '/bugs/%s/take-over';
    private const LIST_URL = '/bugs/list';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        $bug = BugFactory::randomOrCreate(['status' => 'pending_take_over'])->object();
        $url = sprintf($url, $bug->getId());
        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield '[TakeOver] Should redirect to login when not connected' => [self::TAKE_OVER_URL, 302, null, 'http://localhost/login'];
        yield '[TakeOver] Should return 403 when connected as user' => [self::TAKE_OVER_URL, 403, UserFixtures::USER_MAIL];
        yield '[TakeOver] Should return 403 when connected as team member' => [self::TAKE_OVER_URL, 403, UserFixtures::TEAM_USER_MAIL];
        yield '[TakeOver] Should redirect to list when connected as tech team member' => [self::TAKE_OVER_URL, 302, UserFixtures::TECH_TEAM_USER_MAIL, self::LIST_URL];
        yield '[TakeOver] Should redirect to list when connected as admin' => [self::TAKE_OVER_URL, 302, UserFixtures::ADMIN_USER_MAIL, self::LIST_URL];
    }
}
