<?php

namespace App\Tests\EndToEnd;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AbstractBrowserKitEndToEndTest extends WebTestCase
{
    protected AbstractBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * @throws \Exception
     */
    public function testUserIsLoggedIn(): void
    {
        $user = $this->loginUser();
        $this->assertNotNull($user);

        $this->assertStringContainsString('/bugs/list', $this->client->getRequest()->getUri());

        $crawler = $this->client->getCrawler();
        $logoutLink = $crawler->filter('ul.navbar-nav a.nav-link[href="/logout"]');
        $this->assertGreaterThan(
            0,
            $logoutLink->count(),
            'Le lien "/logout" doit être présent dans la navbar après login.'
        );
    }

    /**
     * @param array<string> $roles
     *
     * @throws \Exception
     */
    protected function loginUser(string $email = 'test@test.com', string $password = 'password', array $roles = [User::ROLE_ADMIN]): User
    {
        $user = (new User())
            ->setEmail($email)
            ->setPassword($password)
            ->setRoles($roles);

        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $hashedPassword = $hasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $em = self::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $this->client->followRedirects();

        $crawler = $this->visit('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => $user->getEmail(),
            '_password' => $password,
        ]);
        $this->client->submit($form);

        return $user;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function visit(string $method, string $url, array $parameters = []): Crawler
    {
        return $this->client->request($method, $url, $parameters);
    }
}
