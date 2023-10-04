<?php

namespace App\Tests\Controller\BugController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\BugFactory;

class UnprioritizeTest extends AbstractControllerTest implements TestRouteInterface
{
    private const UNPRIORITIZE_URL = '/bugs/%s/unprioritize';
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
        yield '[Unprioritize] Should redirect to login when not connected' => [self::UNPRIORITIZE_URL, 302, null, 'http://localhost/login'];
        yield '[Unprioritize] Should return 403 when connected as user' => [self::UNPRIORITIZE_URL, 403, UserFixtures::USER_MAIL];
        yield '[Unprioritize] Should return 403 when connected as team member' => [self::UNPRIORITIZE_URL, 403, UserFixtures::TEAM_USER_MAIL];
        yield '[Unprioritize] Should redirect to list when connected as tech team member' => [self::UNPRIORITIZE_URL, 302, UserFixtures::TECH_TEAM_USER_MAIL, self::LIST_URL];
        yield '[Unprioritize] Should redirect to list when connected as admin' => [self::UNPRIORITIZE_URL, 302, UserFixtures::ADMIN_USER_MAIL, self::LIST_URL];
    }
}
