<?php

namespace App\Service;

use App\Entity\Bug;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackActionsBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class NotificationService
{
    public function __construct(
        private readonly ChatterInterface $chatter,
        private readonly RouterInterface $router
    ) {
    }

    public function notifyBug(Bug $bug): void
    {
        $text = $bug->isDone() ? 'Bug résolu' : 'Nouveau bug';
        $chatMessage = (new ChatMessage($text))->options($this->createSlackOptions($bug, $text));

        $this->chatter->send($chatMessage);
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
                        $this->router->generate('bug_show', ['id' => $bug->getId()], UrlGenerator::ABSOLUTE_URL),
                        'primary'
                    ));
    }
}
