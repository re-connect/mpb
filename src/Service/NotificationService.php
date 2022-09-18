<?php

namespace App\Service;

use App\Entity\Bug;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackActionsBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class NotificationService
{
    public function __construct(
        private readonly ChatterInterface $chatter,
        private readonly RouterInterface $router,
        private readonly LoggerInterface $logger,
        private readonly MailerInterface $mailer,
    ) {
    }

    public function notifyBug(Bug $bug): void
    {
        $this->sendResolvedEmail($bug);
        $this->sentSlackMessage($bug);
    }

    private function createSlackOptions(Bug $bug, string $text): SlackOptions
    {
        return (new SlackOptions())
            ->iconEmoji($bug->isDone() ? 'white_check_mark' : 'Bug')
            ->username(sprintf('[MPB] %s', $text))
            ->block((new SlackSectionBlock())->text($bug->getTitle() ?? ''))
            ->block(new SlackDividerBlock())
            ->block(
                (new SlackActionsBlock())
                    ->button(
                        'Voir le bug',
                        $this->router->generate('bug_show', ['id' => $bug->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                        'primary'
                    ));
    }

    public function sentSlackMessage(Bug $bug): void
    {
        $text = $bug->isDone() ? 'Bug résolu' : 'Nouveau bug';

        $chatMessage = (new ChatMessage($text))->options($this->createSlackOptions($bug, $text));
        try {
            $this->chatter->send($chatMessage);
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical(sprintf('Failure sending Slack message, cause: %s', $e->getMessage()));
        }
    }

    private function sendResolvedEmail(Bug $bug): void
    {
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
