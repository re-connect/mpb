<?php

namespace App\Tests\Service;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Form\Model\UserRequestSearch;
use App\Service\BugService;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Zenstruck\Foundry\Test\Factories;

class BugServiceTest extends KernelTestCase
{
    use Factories;

    private BugService $service;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $container = self::getContainer();
        $this->service = $container->get(BugService::class);
    }

    public function testGetAccessible(): void
    {
        $this->loginUser(UserFixtures::USER_MAIL);
        $bugsTeamUser = $this->service->getAccessible(new UserRequestSearch());

        $this->loginUser(UserFixtures::TEAM_USER_MAIL);
        $bugsUser = $this->service->getAccessible(new UserRequestSearch());
        $this->assertGreaterThan(count($bugsTeamUser), count($bugsUser));
    }

    public function testGetDraftsToClean(): void
    {
        $drafts = $this->service->getDraftsToClean();

        foreach ($drafts as $draft) {
            $this->assertEmpty($draft->getTitle());
            $this->assertEmpty($draft->getContent());
            $this->assertTrue($draft->isDraft());
        }
    }

    private function loginUser(string $email): void
    {
        $container = static::getContainer();
        /** @var User $user */
        $user = UserFactory::findOrCreate(['email' => $email])->object();

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $container->get('security.token_storage')->setToken($token);

        $session = $container->get('session.factory')->createSession();
        $session->set('_security_main', serialize($token));
        $session->save();
    }
}
