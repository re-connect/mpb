<?php

namespace App\Tests\Controller\BugController;

use App\DataFixtures\BugFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Bug;
use App\Entity\User;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\BugFactory;
use App\Tests\Factory\UserFactory;

class DeleteTest extends AbstractControllerTest implements TestRouteInterface
{
    private const URL = '/bugs/delete/%s';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        /** @var Bug $bug */
        $bug = BugFactory::randomOrCreate();
        $url = sprintf($url, $bug->getId());
        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield 'Should redirect to login when not connected' => [self::URL, 302, null, 'http://localhost/login', 'POST'];
        yield 'Should redirect to bugs list connected as tech team member' => [self::URL, 302, UserFixtures::TECH_TEAM_USER_MAIL, '/bugs/list',  'POST'];
        yield 'Should redirect to bugs list when connected as admin' => [self::URL, 302, UserFixtures::ADMIN_USER_MAIL, '/bugs/list', 'POST'];
    }

    /**
     * @param array<string> $roles
     *
     * @dataProvider provideTestUserCanNotDeleteBugHeDoesNotOwn
     */
    public function testUserCanNotDeleteBugHeDoesNotOwn(array $roles): void
    {
        /** @var Bug $bug */
        $bug = BugFactory::randomOrCreate(['draft' => false]);
        /** @var User $user */
        $user = UserFactory::createOne(['roles' => $roles]);
        $url = sprintf(self::URL, $bug->getId());
        $this->assertRoute($url, 403, $user->getEmail(), null, 'POST');
    }

    public function provideTestUserCanNotDeleteBugHeDoesNotOwn(): \Generator
    {
        yield 'Basic user can not delete bug he does not own' => [[User::ROLE_USER]];
        yield 'Team user can not delete bug he does not own' => [[User::ROLE_TEAM]];
    }

    /**
     * @param array<string> $roles
     *
     * @dataProvider provideTestUserCanDeleteBugHeOwns
     */
    public function testUserCanDeleteBugHeOwns(array $roles, string $bugTitle): void
    {
        /** @var Bug $bug */
        $bug = BugFactory::findOrCreate(['title' => $bugTitle]);
        /** @var User $user */
        $user = $bug->getUser();
        $this->assertEquals($roles, $user->getRoles());

        $url = sprintf(self::URL, $bug->getId());
        $this->assertRoute($url, 302, $user->getEmail(), '/bugs/list', 'POST');
    }

    public function provideTestUserCanDeleteBugHeOwns(): \Generator
    {
        yield 'Basic user can delete bug he owns' => [[User::ROLE_USER],  BugFixtures::BUG_FROM_BASIC_USER];
        yield 'Team user can delete bug he owns' => [[User::ROLE_TEAM],  BugFixtures::BUG_FROM_TEAM_USER];
    }
}
