<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testDashboardAndLogout(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertResponseRedirects('/login');

        /** @var \App\Entity\User $user */
        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'tester@gmail.com']);
        $client->loginUser($user);

        $client->request('GET', '/');
        $this->assertResponseRedirects('/bugs/list');

        $client->request('GET', '/logout');
        $this->assertResponseRedirects('');

        $client->request('GET', '/');
        $this->assertResponseRedirects('/login');
    }
}
