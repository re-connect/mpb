<?php

namespace App\Tests\Controller\BugController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\BugFactory;

class SolveTest extends AbstractControllerTest implements TestRouteInterface
{
    private const SOLVE_URL = '/bugs/%s/solve';
    private const LIST_URL = '/bugs/list';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        $bug = BugFactory::randomOrCreate(['status' => 'pending'])->object();
        $url = sprintf($url, $bug->getId());
        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield '[Solve] Should redirect to login when not connected' => [self::SOLVE_URL, 302, null, 'http://localhost/login'];
        yield '[Solve] Should return 403 when connected as user' => [self::SOLVE_URL, 403, UserFixtures::USER_MAIL];
        yield '[Solve] Should return 403 when connected as team member' => [self::SOLVE_URL, 403, UserFixtures::TEAM_USER_MAIL];
        yield '[Solve] Should redirect to list when connected as tech team member' => [self::SOLVE_URL, 302, UserFixtures::TECH_TEAM_USER_MAIL, self::LIST_URL];
        yield '[Solve] Should redirect to list when connected as admin' => [self::SOLVE_URL, 302, UserFixtures::ADMIN_USER_MAIL, self::LIST_URL];
    }
}
