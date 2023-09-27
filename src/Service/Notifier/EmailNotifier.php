<?php

namespace App\Service\Notifier;

use App\Entity\Bug;
use App\Entity\UserRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class EmailNotifier implements ChannelNotifierInterface
{
    public function __construct(private LoggerInterface $logger, private MailerInterface $mailer)
    {
    }

    public function notify(UserRequest $request): void
    {
        if (!$request->isBug()) {
            return;
        }
        /** @var Bug $bug */
        $bug = $request;
        if (!$bug->isDone() || !$bug->getUser()?->getEmail()) {
            return;
        }

        $email = (new Email())
            ->from('contact@reconnect.fr')
            ->to($bug->getUser()->getEmail())
            ->subject('[MPB] Bug résolu')
            ->html(sprintf('<p>Le bug suivant a été résolu :<br/><br/>%s</p>', $bug->getTitle()));

        try {
            $this->mailer->send($email);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            $this->logger->critical(sprintf('Failure sending Slack message, cause: %s', $e->getMessage()));
        }
    }
}
