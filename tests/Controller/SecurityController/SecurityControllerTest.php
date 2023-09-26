<?php

namespace App\Tests\Controller\SecurityController;

use App\DataFixtures\UserFixtures;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;

class SecurityControllerTest extends AbstractControllerTest implements TestRouteInterface
{
    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield 'Home page should redirect to login when not connected' => ['/', 302, null, 'http://localhost/login'];
        yield 'Home page should redirect to bugs list when not connected' => ['/', 302, UserFixtures::USER_MAIL, '/bugs/list'];
        yield 'Logout page should redirect to home' => ['/logout', 302, UserFixtures::USER_MAIL, 'http://localhost/'];
    }
}
