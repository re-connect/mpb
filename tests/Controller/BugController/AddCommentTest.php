<?php

namespace App\Tests\Controller\BugController;

use App\DataFixtures\BugFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Bug;
use App\Entity\User;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestFormInterface;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\BugFactory;
use App\Tests\Factory\UserFactory;

class AddCommentTest extends AbstractControllerTest implements TestRouteInterface, TestFormInterface
{
    private const URL = '/bugs/%s/add-comment';

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
        yield 'Should return 200 when connected as team member' => [self::URL, 200, UserFixtures::TEAM_USER_MAIL];
        yield 'Should return 200 when connected as tech team member' => [self::URL, 200, UserFixtures::TECH_TEAM_USER_MAIL];
        yield 'Should return 200 when connected as admin' => [self::URL, 200, UserFixtures::ADMIN_USER_MAIL];
    }

    public function testUserCanNotCommentBugHeDoesNotOwn(): void
    {
        /** @var Bug $bug */
        $bug = BugFactory::randomOrCreate();
        /** @var User $user */
        $user = UserFactory::createOne();
        $url = sprintf(self::URL, $bug->getId());
        $this->assertRoute($url, 403, $user->getEmail());
    }

    public function testUserCanCommentBugHeOwns(): void
    {
        /** @var Bug $bug */
        $bug = BugFactory::findOrCreate(['title' => BugFixtures::BUG_FROM_BASIC_USER]);
        /** @var User $user */
        $user = $bug->getUser();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $url = sprintf(self::URL, $bug->getId());
        $this->assertRoute($url, 200, $user->getEmail());
    }

    /**
     * @dataProvider provideTestFormIsValid
     * @param array<string, string> $values
     */
    public function testFormIsValid(string $url, string $formSubmit, array $values, ?string $email, ?string $redirectUrl): void
    {
        $bug = BugFactory::randomOrCreate()->object();
        $url = sprintf($url, $bug->getId());
        $this->assertFormIsValid($url, $formSubmit, $values, $email, $url);
    }

    public function provideTestFormIsValid(): \Generator
    {
        yield 'Page should refresh when form is valid' => [
            self::URL,
            'add_comment',
            ['comment[content]' => 'Comment'],
            UserFixtures::TECH_TEAM_USER_MAIL,
            null,
        ];
    }

    /**
     * @dataProvider provideTestFormIsNotValid
     * @param array<string, string> $values
     * @param array<int, array<string, string|array<string, string>>> $errors
     */
    public function testFormIsNotValid(string $url, string $route, string $formSubmit, array $values, array $errors, ?string $email, string $alternateSelector = null): void
    {
        $bug = BugFactory::randomOrCreate()->object();
        $url = sprintf($url, $bug->getId());
        $this->assertFormIsNotValid($url, $route, $formSubmit, $values, $errors, $email, $alternateSelector);
    }

    public function provideTestFormIsNotValid(): \Generator
    {
        yield 'Should return an error when content is empty' => [
            self::URL,
            'bug_add_comment',
            'add_comment',
            ['comment[content]' => ''],
            [
                [
                    'message' => 'This value should not be blank.',
                    'params' => [],
                ],
            ],
            UserFixtures::TECH_TEAM_USER_MAIL,
        ];
    }
}
