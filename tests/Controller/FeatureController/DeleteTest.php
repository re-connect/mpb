<?php

namespace App\Tests\Controller\FeatureController;

use App\DataFixtures\FeatureFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Feature;
use App\Entity\User;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\FeatureFactory;
use App\Tests\Factory\UserFactory;

class DeleteTest extends AbstractControllerTest implements TestRouteInterface
{
    private const URL = '/features/delete/%s';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, ?string $userEmail = null, ?string $expectedRedirect = null, string $method = 'GET'): void
    {
        /** @var Feature $feature */
        $feature = FeatureFactory::randomOrCreate();
        $url = sprintf($url, $feature->getId());
        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield 'Should redirect to login when not connected' => [self::URL, 302, null, 'http://localhost/login', 'POST'];
        yield 'Should redirect to bugs list connected as tech team member' => [self::URL, 302, UserFixtures::TECH_TEAM_USER_MAIL, '/features/list',  'POST'];
        yield 'Should redirect to bugs list when connected as admin' => [self::URL, 302, UserFixtures::ADMIN_USER_MAIL, '/features/list', 'POST'];
    }

    /**
     * @param array<string> $roles
     *
     * @dataProvider provideTestUserCanNotDeleteFeatureHeDoesNotOwn
     */
    public function testUserCanNotDeleteFeatureHeDoesNotOwn(array $roles): void
    {
        /** @var Feature $feature */
        $feature = FeatureFactory::randomOrCreate();
        /** @var User $user */
        $user = UserFactory::createOne(['roles' => $roles]);
        $url = sprintf(self::URL, $feature->getId());
        $this->assertRoute($url, 403, $user->getEmail(), null, 'POST');
    }

    public function provideTestUserCanNotDeleteFeatureHeDoesNotOwn(): \Generator
    {
        yield 'Basic user can not delete feature he does not own' => [[User::ROLE_USER]];
        yield 'Team user can not delete feature he does not own' => [[User::ROLE_TEAM]];
    }

    /**
     * @param array<string> $roles
     *
     * @dataProvider provideTestUserCanDeleteFeatureHeOwns
     */
    public function testUserCanDeleteFeatureHeOwns(array $roles, string $featureTitle): void
    {
        /** @var Feature $feature */
        $feature = FeatureFactory::findOrCreate(['title' => $featureTitle]);
        /** @var User $user */
        $user = $feature->getUser();
        $this->assertEquals($roles, $user->getRoles());

        $url = sprintf(self::URL, $feature->getId());
        $this->assertRoute($url, 302, $user->getEmail(), '/features/list', 'POST');
    }

    public function provideTestUserCanDeleteFeatureHeOwns(): \Generator
    {
        yield 'Basic user can delete feature he owns' => [[User::ROLE_USER],  FeatureFixtures::FEATURE_FROM_BASIC_USER];
        yield 'Team user can delete feature he owns' => [[User::ROLE_TEAM],  FeatureFixtures::FEATURE_FROM_TEAM_USER];
    }
}
