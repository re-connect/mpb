<?php

namespace App\Tests\Service;

use App\Repository\UserRepository;
use App\Service\BugReportService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BugReportServiceTest extends KernelTestCase
{
    private BugReportService $service;

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
        $this->service = $container->get(BugReportService::class);
    }

    /**
     * @dataProvider userAgents
     */
    public function testInitBugReport(string $a): void
    {
        $bugReport = $this->service->initBugReport($a);

        $this->assertIsObject($bugReport);
        $this->assertEquals($a, $bugReport->getUserAgent());
        $this->assertEquals(0, $bugReport->getApplication());
    }

    public function testCreate(): void
    {
        $this->loginUser('tester_team@gmail.com');

        $bugReport = $this->service->initBugReport('');

        $this->assertNull($bugReport->getCreatedAt());

        $this->service->create($bugReport);

        $this->assertNotNull($bugReport->getCreatedAt());
        $this->assertFalse($bugReport->isDone());
    }

    public function testGetAccessible(): void
    {
        $this->loginUser('tester_team@gmail.com');
        $bugReportsTeamUser = $this->service->getAccessible();

        $this->loginUser('tester@gmail.com');
        $bugReportsUser = $this->service->getAccessible();

        $this->assertGreaterThan(count($bugReportsUser), count($bugReportsTeamUser));
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
