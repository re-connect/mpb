<?php

namespace App\Tests\Service\Notifier;

use App\Service\Notifier\EmailNotifier;
use App\Service\Notifier\UserRequestEmailGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zenstruck\Foundry\Test\Factories;

abstract class AbstractNotifierTest extends KernelTestCase
{
    use Factories;

    protected EmailNotifier $emailNotifier;
    protected UserRequestEmailGenerator $emailGenerator;
    protected UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->getContainer()->get(UrlGeneratorInterface::class);
        $this->emailGenerator = $this->getContainer()->get(UserRequestEmailGenerator::class);
        $this->emailNotifier = new EmailNotifier(
            $this->createMock(LoggerInterface::class),
            $this->getContainer()->get(MailerInterface::class),
            $this->emailGenerator,
        );
    }

    /**
     * @param array<string> $textBodyElements
     */
    public function assertEmail(RawMessage $email, string $recipients, array $textBodyElements): void
    {
        $this->assertEmailHeaderSame($email, 'To', $recipients);
        foreach ($textBodyElements as $element) {
            self::assertEmailHtmlBodyContains($email, $element);
        }
    }
}
