<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class SecurityControllerTest extends WebTestCase
{
    use Factories;

    public function testDashboardAndLogout(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseRedirects('http://localhost/login');

        $user = UserFactory::findOrCreateWithRole(User::ROLE_USER)->object();
        $client->loginUser($user);

        $client->request('GET', '/');
        $this->assertResponseRedirects('/bugs/list');

        $client->request('GET', '/logout');
        $this->assertResponseRedirects('');

        $client->request('GET', '/');
        $this->assertResponseRedirects('http://localhost/login');
    }
}
