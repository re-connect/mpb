<?php

namespace App\Tests\Controller\BugController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\BugFactory;
use App\Tests\Factory\FeatureFactory;

class ConvertToFeatureTest extends AbstractControllerTest implements TestRouteInterface
{
    private const URL = '/bugs/%s/convert-to-feature';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        $bug = BugFactory::randomOrCreate()->object();
        $url = sprintf($url, $bug->getId());

        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield 'Should redirect to login when not connected' => [self::URL, 302, null, 'http://localhost/login'];
        yield 'Should return 403 when connected as user' => [self::URL, 403, UserFixtures::USER_MAIL];
        yield 'Should return 403 when connected as team member' => [self::URL, 403, UserFixtures::TEAM_USER_MAIL];
        yield 'Should redirect to created feature when connected as tech team member' => [self::URL, 302, UserFixtures::TECH_TEAM_USER_MAIL];
        yield 'Should redirect to created feature when connected as admin' => [self::URL, 302, UserFixtures::ADMIN_USER_MAIL];
    }

    public function testFeatureIsCreated(): void
    {
        $bug = BugFactory::randomOrCreate(['done' => false])->object();
        $url = sprintf(self::URL, $bug->getId());
        $featuresCount = FeatureFactory::count();

        $this->assertRoute($url, 302, UserFixtures::TECH_TEAM_USER_MAIL);

        $newFeature = FeatureFactory::last();
        self::assertEquals(FeatureFactory::count(), $featuresCount + 1);
        self::assertTrue(BugFactory::find($bug)->object()->isDone());
        self::assertFalse($newFeature->isDraft());
    }
}
