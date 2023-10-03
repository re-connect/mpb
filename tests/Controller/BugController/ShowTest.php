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

class ShowTest extends AbstractControllerTest implements TestRouteInterface
{
    private const URL = '/bugs/%s';

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

    public function testUserCanNotShowBugHeDoesNotOwn(): void
    {
        /** @var Bug $bug */
        $bug = BugFactory::randomOrCreate();
        /** @var User $user */
        $user = UserFactory::createOne();
        $url = sprintf(self::URL, $bug->getId());
        $this->assertRoute($url, 403, $user->getEmail());
    }

    public function testUserCanShowBugHeOwns(): void
    {
        /** @var Bug $bug */
        $bug = BugFactory::findOrCreate(['title' => BugFixtures::BUG_FROM_BASIC_USER]);
        /** @var User $user */
        $user = $bug->getUser();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $url = sprintf(self::URL, $bug->getId());
        $this->assertRoute($url, 200, $user->getEmail());
    }

    /** @dataProvider provideTestCanTakeOverIsAccessible */
    public function testCanTakeOverIsAccessible(string $email, bool $shouldAccessButton): void
    {
        $clientTest = static::createClient();

        $user = UserFactory::findOrCreate(['email' => $email])->object();
        $clientTest->loginUser($user);

        $bug = BugFactory::randomOrCreate(['assignee' => null, 'done' => false, 'draft' => false])->object();
        $clientTest->request('GET', sprintf(self::URL, $bug->getId()));

        $shouldAccessButton ? $this->assertSelectorExists('i.fa-truck-fast') : $this->assertSelectorNotExists('i.fa-truck-fast');
    }

    public function provideTestCanTakeOverIsAccessible(): \Generator
    {
        yield 'User should not access button' => [UserFixtures::USER_MAIL, false];
        yield 'Team user should not access button' => [UserFixtures::TEAM_USER_MAIL, false];
        yield 'Tech team user should access button' => [UserFixtures::TECH_TEAM_USER_MAIL, true];
        yield 'Admin user should access button' => [UserFixtures::ADMIN_USER_MAIL, true];
    }

    /** @dataProvider provideTestMarkDoneIsAccessible */
    public function testMarkDoneIsAccessible(string $email, bool $shouldAccessButton): void
    {
        $clientTest = static::createClient();

        $user = UserFactory::findOrCreate(['email' => $email])->object();
        $clientTest->loginUser($user);

        $bug = BugFactory::randomOrCreate(['done' => false])->object();
        $clientTest->request('GET', sprintf(self::URL, $bug->getId()));

        $shouldAccessButton ? $this->assertSelectorExists('i.fa-check') : $this->assertSelectorNotExists('i.fa-check');
    }

    public function provideTestMarkDoneIsAccessible(): \Generator
    {
        yield 'User should not access button' => [UserFixtures::USER_MAIL, false];
        yield 'Team user should not access button' => [UserFixtures::TEAM_USER_MAIL, false];
        yield 'Tech team user should access button' => [UserFixtures::TECH_TEAM_USER_MAIL, true];
        yield 'Admin user should access button' => [UserFixtures::ADMIN_USER_MAIL, true];
    }
}
