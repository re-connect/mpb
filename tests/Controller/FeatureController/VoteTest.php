<?php

namespace App\Tests\Controller\FeatureController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\FeatureFactory;

class VoteTest extends AbstractControllerTest implements TestRouteInterface
{
    private const VOTE_URL = '/features/%s/vote';
    private const LIST_URL = '/features/list';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        $feature = FeatureFactory::randomOrCreate()->object();
        $url = sprintf($url, $feature->getId());
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
