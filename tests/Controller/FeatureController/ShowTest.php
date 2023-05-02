<?php

namespace App\Tests\Controller\FeatureController;

use App\DataFixtures\UserFixtures;
use App\Entity\FeatureStatus;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\FeatureFactory;
use App\Tests\Factory\UserFactory;

class ShowTest extends AbstractControllerTest implements TestRouteInterface
{
    private const URL = '/features/%s';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, ?string $userEmail = null, ?string $expectedRedirect = null, string $method = 'GET'): void
    {
        $feature = FeatureFactory::randomOrCreate()->object();
        $url = sprintf($url, $feature->getId());
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

    /**  @dataProvider provideTestFormIsValid */
    public function testFormIsValid(string $url, string $formSubmit, array $values, ?string $email, ?string $redirectUrl): void
    {
        $feature = FeatureFactory::randomOrCreate()->object();
        $url = sprintf($url, $feature->getId());
        $this->assertFormIsValid($url, $formSubmit, $values, $email, $url);
    }

    public function provideTestFormIsValid(): \Generator
    {
        yield 'Page should refresh when form is valid' => [
            self::URL,
            'edit_status',
            ['feature_status[status]' => FeatureStatus::BeingDeveloped->value],
            UserFixtures::TECH_TEAM_USER_MAIL,
            null,
        ];
    }

    /** @dataProvider provideTestFormIsAccessible */
    public function testFormIsAccessible(string $email, bool $shouldAccessForm): void
    {
        $clientTest = static::createClient();

        $user = UserFactory::findOrCreate(['email' => $email])->object();
        $clientTest->loginUser($user);

        $feature = FeatureFactory::randomOrCreate()->object();
        $clientTest->request('GET', sprintf(self::URL, $feature->getId()));

        $shouldAccessForm ? $this->assertSelectorExists('form') : $this->assertSelectorNotExists('form');
    }

    public function provideTestFormIsAccessible(): \Generator
    {
        yield 'User should not access form' => [UserFixtures::USER_MAIL, false];
        yield 'Team user should not access form' => [UserFixtures::TEAM_USER_MAIL, false];
        yield 'Tech team user should access form' => [UserFixtures::TECH_TEAM_USER_MAIL, true];
        yield 'Admin user should access form' => [UserFixtures::ADMIN_USER_MAIL, true];
    }
}
