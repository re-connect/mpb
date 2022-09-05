<?php

namespace App\Tests\Service;

use App\Repository\UserRepository;
use App\Service\BugService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BugServiceTest extends KernelTestCase
{
    private BugService $service;

    /**
     * @return \Generator<string[]>
     */
    public function userAgents(): \Generator
    {
        yield [''];
        yield ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36'];
    }

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $container = self::getContainer();
        $this->loginUser('tester@gmail.com');
        $this->service = $container->get(BugService::class);
    }

    /**
     * @dataProvider userAgents
     */
    public function testInitBug(string $a): void
    {
        $bug = $this->service->initBug($a);

        $this->assertIsObject($bug);
        $this->assertEquals($a, $bug->getUserAgent());
        $this->assertEquals(0, $bug->getApplication());
    }

    public function testCreate(): void
    {
        $this->loginUser('tester_team@gmail.com');
        $bug = $this->service->initBug('');

        $this->assertEmpty($bug->getUserAgent());
        $this->assertTrue($bug->isDraft());

        $this->service->create($bug);
        $this->assertFalse($bug->isDraft());
        $this->assertNotNull($bug->getCreatedAt());
        $this->assertFalse($bug->isDone());
    }

    public function testGetAccessible(): void
    {
        $this->loginUser('tester_team@gmail.com');
        $bugsTeamUser = $this->service->getAccessible();

        $this->loginUser('tester@gmail.com');
        $bugsUser = $this->service->getAccessible();

        $this->assertGreaterThan(count($bugsUser), count($bugsTeamUser));
    }

    private function loginUser(string $email): void
    {
        $container = static::getContainer();
        /** @var \App\Entity\User $user */
        $user = $container->get(UserRepository::class)->findOneBy(['email' => $email]);

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $container->get('security.token_storage')->setToken($token);

        $session = $container->get('session.factory')->createSession();
        $session->set('_security_main', serialize($token));
        $session->save();
    }
}
