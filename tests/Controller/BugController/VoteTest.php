<?php

namespace App\Tests\Controller\BugController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\BugFactory;

class VoteTest extends AbstractControllerTest implements TestRouteInterface
{
    private const VOTE_URL = '/bugs/%s/vote';
    private const LIST_URL = '/bugs/list';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        $bug = BugFactory::randomOrCreate()->object();
        $url = sprintf($url, $bug->getId());
        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield '[Vote] Should redirect to login when not connected' => [self::VOTE_URL, 302, null, 'http://localhost/login'];
        yield '[Vote] Should redirect to list when connected as user' => [self::VOTE_URL, 302, UserFixtures::USER_MAIL, self::LIST_URL];
        yield '[Vote] Should redirect to list when connected as team member' => [self::VOTE_URL, 302, UserFixtures::TEAM_USER_MAIL, self::LIST_URL];
        yield '[Vote] Should redirect to list when connected as tech team member' => [self::VOTE_URL, 302, UserFixtures::TECH_TEAM_USER_MAIL, self::LIST_URL];
        yield '[Vote] Should redirect to list when connected as admin' => [self::VOTE_URL, 302, UserFixtures::ADMIN_USER_MAIL, self::LIST_URL];
    }
}
