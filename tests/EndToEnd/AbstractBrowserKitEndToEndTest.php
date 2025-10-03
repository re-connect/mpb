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
     * @param string $email
     * @param string $password
     * @param array<string> $roles
     * @throws \Exception
     */
    protected function loginUser(string $email = 'test@test.com', string $password = 'password', array $roles = [User::ROLE_ADMIN]): void
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

        $this->assertNotNull($user);

        $this->client->followRedirects();

        $crawler = $this->visit('/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => $user->getEmail(),
            '_password' => $password,
        ]);
        $this->client->submit($form);

        $this->assertStringContainsString('/bugs/list', $this->client->getRequest()->getUri());
    }

    protected function visit(string $url): Crawler
    {
        return $this->client->request('GET', $url);
    }

}