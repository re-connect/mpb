<?php

namespace App\Tests\Controller;

interface TestRouteInterface
{
    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, ?string $userEmail = null, ?string $expectedRedirect = null, string $method = 'GET'): void;

    public function provideTestRoute(): \Generator;
}
