<?php

namespace App\Tests\Controller\FeatureController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestFormInterface;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\FeatureFactory;

class AddCommentTest extends AbstractControllerTest implements TestRouteInterface, TestFormInterface
{
    private const URL = '/features/%s/add-comment';

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
            'add_comment',
            ['comment[content]' => 'Comment'],
            UserFixtures::USER_MAIL,
            null,
        ];
    }

    /**
     * @param array<string, string> $values
     * @param array<array>          $errors
     *
     * @dataProvider provideTestFormIsNotValid
     */
    public function testFormIsNotValid(string $url, string $route, string $formSubmit, array $values, array $errors, ?string $email, ?string $alternateSelector = null): void
    {
        $feature = FeatureFactory::randomOrCreate()->object();
        $url = sprintf($url, $feature->getId());
        $this->assertFormIsNotValid($url, $route, $formSubmit, $values, $errors, $email, $alternateSelector);
    }

    public function provideTestFormIsNotValid(): \Generator
    {
        yield 'Should return an error when content is empty' => [
            self::URL,
            'feature_add_comment',
            'add_comment',
            ['comment[content]' => ''],
            [
                [
                    'message' => 'This value should not be blank.',
                    'params' => [],
                ],
            ],
            UserFixtures::USER_MAIL,
        ];
    }
}
