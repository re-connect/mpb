<?php

namespace App\Tests\Controller\FeatureController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestFormInterface;
use App\Tests\Controller\TestRouteInterface;

class CreateTest extends AbstractControllerTest implements TestRouteInterface, TestFormInterface
{
    private const URL = '/features/create';
    private const FORM_VALUES = [
        'feature[title]' => 'Titre',
        'feature[content]' => 'Description',
        'feature[application]' => 1,
    ];

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

    /**  @dataProvider provideTestFormIsValid */
    public function testFormIsValid(string $url, string $formSubmit, array $values, ?string $email, ?string $redirectUrl): void
    {
        $this->assertFormIsValid($url, $formSubmit, $values, $email, $redirectUrl);
    }

    public function provideTestFormIsValid(): \Generator
    {
        yield 'Page should redirect to list when form is valid' => [
            self::URL,
            'create',
            self::FORM_VALUES,
            UserFixtures::USER_MAIL,
            '/features/list',
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
        $this->assertFormIsNotValid($url, $route, $formSubmit, $values, $errors, $email, $alternateSelector);
    }

    public function provideTestFormIsNotValid(): \Generator
    {
        $values = self::FORM_VALUES;
        $values['feature[title]'] = null;
        yield 'Should return an error when title is empty' => [
            self::URL,
            'feature_new',
            'create',
            $values,
            [
                [
                    'message' => 'This value should not be blank.',
                    'params' => [],
                ],
            ],
            UserFixtures::USER_MAIL,
        ];

        $values = self::FORM_VALUES;
        $values['feature[content]'] = null;
        yield 'Should return an error when content is empty' => [
            self::URL,
            'feature_new',
            'create',
            $values,
            [
                [
                    'message' => 'This value should not be blank.',
                    'params' => [],
                ],
            ],
            UserFixtures::USER_MAIL,
        ];

        $values = self::FORM_VALUES;
        $values['feature[application]'] = '';
        yield 'Should return an error when application is empty' => [
            self::URL,
            'feature_new',
            'create',
            $values,
            [
                [
                    'message' => 'This value should not be null.',
                    'params' => [],
                ],
            ],
            UserFixtures::USER_MAIL,
        ];
    }
}
