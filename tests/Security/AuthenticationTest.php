<?php

namespace App\Tests\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?KernelBrowser $client;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        /** @phpstan-ignore-next-line */
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        if (null === $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'gandalf@gmail.com'])) {
            $user = (new User())
                ->setEmail('gandalf@gmail.com')
                ->setFirstName('Gandalf')
                ->setLastName('The Grey')
                ->setLastLogin(new \DateTime('now'))
                ->setPassword('testpassword')
                ->addRole('ROLE_USER');
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        self::ensureKernelShutdown();
        $this->client = self::createClient();
    }

    public function testUserNotLoggedIn(): void
    {
        $this->client->request('GET', '/bug-report/list');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects($this->client->getResponse()->headers->get('Location'));
    }

    public function testUserLogin(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'gandalf@gmail.com']);
        $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->client->loginUser($user);
        $this->client->request('GET', '/bug-report/list');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
