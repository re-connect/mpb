<?php

namespace App\Service\Notifier;

use App\Entity\UserRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class EmailNotifier implements ChannelNotifierInterface
{
    public function __construct(private LoggerInterface $logger, private MailerInterface $mailer)
    {
    }

    public function notify(UserRequest $request): void
    {
        if (!$request->isDone() || !$request->getUser()?->getEmail()) {
            return;
        }

        $email = $this->buildEmail(
            $request,
            $request->isBug() ? 'Bug résolu' : 'Fonctionnalité développée',
            $request->isBug() ? 'Le bug suivant a été résolu' : 'La fonctionnalité suivante a été développée',
        );

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical(sprintf('Failure sending Slack message, cause: %s', $e->getMessage()));
        }
    }

    public function buildEmail(UserRequest $request, string $subject, string $body): Email
    {
        return (new Email())
            ->from('contact@reconnect.fr')
            ->to($request->getUser()->getEmail())
            ->subject(sprintf('[MPB] %s', $subject))
            ->html(body: sprintf('<p>%s :<br/><br/>%s</p>', $body, $request->getTitle()));
    }
}
