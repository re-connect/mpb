<?php

namespace App\Service\Notifier;

use App\Entity\Bug;
use App\Entity\UserRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackActionsBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

readonly class SlackNotifier implements ChannelNotifierInterface
{
    private const CHANNELS = ['bugs' => 'C02UDN5PZFZ', 'features' => 'C05TWL4N3R9'];

    public function __construct(private ChatterInterface $chatter, private RouterInterface $router, private LoggerInterface $logger)
    {
    }

    private function createSlackOptions(Bug $bug): SlackOptions
    {
        return (new SlackOptions())
            ->iconEmoji($bug->isDone() ? 'white_check_mark' : 'Bug')
            ->username(sprintf('[MPB] %s', $bug->isDone() ? 'Bug résolu' : 'Nouveau bug'))
            ->block((new SlackSectionBlock())->text($bug->getTitle() ?? ''))
            ->block(new SlackDividerBlock())
            ->block(
                (new SlackActionsBlock())
                    ->button(
                        'Voir le bug',
                        $this->router->generate('bug_show', ['id' => $bug->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                        'primary'
                    ))
            ->recipient(self::CHANNELS['bugs']);
    }

    public function notify(UserRequest $request): void
    {
        try {
            $chatMessage = $this->buildChatMessage($request);
            if ($chatMessage) {
                $this->chatter->send($chatMessage);
            }
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical(sprintf('Failure sending Slack message, cause: %s', $e->getMessage()));
        }
    }

    public function buildChatMessage(UserRequest $request): ?ChatMessage
    {
        if ($request->isBug()) {
            /** @var Bug $request */
            return $this->buildBugMessage($request);
        }

        return null;
    }

    public function buildBugMessage(Bug $bug): ChatMessage
    {
        return (new ChatMessage($bug->isDone() ? 'Bug résolu' : 'Nouveau bug'))->options($this->createSlackOptions($bug));
    }
}
