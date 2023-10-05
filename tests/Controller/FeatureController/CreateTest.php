<?php

namespace App\Tests\Controller\FeatureController;

use App\DataFixtures\FeatureFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Feature;
use App\Entity\User;
use App\Repository\ApplicationRepository;
use App\Repository\RequesterRepository;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestFormInterface;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\FeatureFactory;
use App\Tests\Factory\UserFactory;

class CreateTest extends AbstractControllerTest implements TestRouteInterface, TestFormInterface
{
    private const URL = '/features/create/%s';

    private const FORM_VALUES = [
        'feature[title]' => 'Titre',
        'feature[content]' => 'Description',
    ];

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        /** @var Feature $feature */
        $feature = FeatureFactory::randomOrCreate(['draft' => true]);
        $url = sprintf($url, $feature->getId());
        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield 'Should redirect to login when not connected' => [self::URL, 302, null, 'http://localhost/login'];
        yield 'Should return 200 when connected as tech team member' => [self::URL, 200, UserFixtures::TECH_TEAM_USER_MAIL];
        yield 'Should return 200 when connected as admin' => [self::URL, 200, UserFixtures::ADMIN_USER_MAIL];
    }

    /**
     * @param array<string> $roles
     *
     * @dataProvider provideTestUserCanNotCreateFeatureHeDoesNotOwn
     */
    public function testUserCanNotCreateFeatureHeDoesNotOwn(array $roles): void
    {
        /** @var Feature $feature */
        $feature = FeatureFactory::randomOrCreate();
        /** @var User $user */
        $user = UserFactory::createOne(['roles' => $roles]);
        $url = sprintf(self::URL, $feature->getId());
        $this->assertRoute($url, 403, $user->getEmail());
    }

    public function provideTestUserCanNotCreateFeatureHeDoesNotOwn(): \Generator
    {
        yield 'Basic user can not create feature he does not own' => [[User::ROLE_USER]];
        yield 'Team user can not create feature he does not own' => [[User::ROLE_TEAM]];
    }

    /**
     * @param array<string> $roles
     *
     * @dataProvider provideTestUserCanCreateFeatureHeOwns
     */
    public function testUserCanCreateFeatureHeOwns(array $roles, string $featureTitle): void
    {
        /** @var Feature $feature */
        $feature = FeatureFactory::findOrCreate(['title' => $featureTitle]);
        /** @var User $user */
        $user = $feature->getUser();
        $this->assertEquals($roles, $user->getRoles());

        $url = sprintf(self::URL, $feature->getId());
        $this->assertRoute($url, 200, $user->getEmail());
    }

    public function provideTestUserCanCreateFeatureHeOwns(): \Generator
    {
        yield 'Basic user can create feature he owns' => [[User::ROLE_USER],  FeatureFixtures::DRAFT_FROM_BASIC_USER];
        yield 'Team user can create feature he owns' => [[User::ROLE_TEAM],  FeatureFixtures::DRAFT_FROM_TEAM_USER];
    }

    /**
     * @param array<string, string> $values
     * @dataProvider provideTestFormIsValid
     */
    public function testFormIsValid(string $url, string $formSubmit, array $values, ?string $email, ?string $redirectUrl): void
    {
        /** @var Feature $feature */
        $feature = FeatureFactory::randomOrCreate(['draft' => true]);
        $url = sprintf($url, $feature->getId());
        $this->assertFormIsValid($url, $formSubmit, $values, $email, $redirectUrl);
    }

    public function provideTestFormIsValid(): \Generator
    {
        $values = self::FORM_VALUES;
        $values['feature[application]'] = $this->getContainer()->get(ApplicationRepository::class)->findAll()[0]->getId();
        $values['feature[requestedBy]'] = $this->getContainer()->get(RequesterRepository::class)->findAll()[0]->getId();
        yield 'Page should redirect to list when form is valid' => [
            self::URL,
            'create',
            $values,
            UserFixtures::TECH_TEAM_USER_MAIL,
            '/features/list',
        ];
    }

    /**
     * @param array<string, string> $values
     * @param array<int, array<string, string|array<string, string>>> $errors
     *
     * @dataProvider provideTestFormIsNotValid
     */
    public function testFormIsNotValid(string $url, string $route, string $formSubmit, array $values, array $errors, ?string $email, string $alternateSelector = null): void
    {
        /** @var Feature $feature */
        $feature = FeatureFactory::randomOrCreate(['draft' => true]);
        $url = sprintf($url, $feature->getId());
        $this->assertFormIsNotValid($url, $route, $formSubmit, $values, $errors, $email, $alternateSelector);
    }

    public function provideTestFormIsNotValid(): \Generator
    {
        $values = self::FORM_VALUES;
        $values['feature[application]'] = $this->getContainer()->get(ApplicationRepository::class)->findAll()[0]->getId();
        $values['feature[requestedBy]'] = $this->getContainer()->get(RequesterRepository::class)->findAll()[0]->getId();
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
            UserFixtures::TECH_TEAM_USER_MAIL,
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
            UserFixtures::TECH_TEAM_USER_MAIL,
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
            UserFixtures::TECH_TEAM_USER_MAIL,
        ];
    }
}
