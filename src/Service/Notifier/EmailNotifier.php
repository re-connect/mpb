<?php

namespace App\Service\Notifier;

use App\Entity\UserRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

readonly class EmailNotifier implements ChannelNotifierInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private MailerInterface $mailer,
        private UserRequestEmailGenerator $emailGenerator,
    ) {
    }

    public function notify(UserRequest $request): void
    {
        if (!$request->isDone() || !$request->getUser()?->getEmail()) {
            return;
        }

        try {
            $email = $this->emailGenerator->generate($request);
            if ($email) {
                $this->mailer->send($email);
            }
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical(sprintf('Failure sending Slack message, cause: %s', $e->getMessage()));
        }
    }
}
