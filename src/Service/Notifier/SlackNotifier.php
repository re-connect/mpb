<?php

namespace App\Service\Notifier;

use App\Entity\Bug;
use App\Entity\Feature;
use App\Entity\UserRequest;
use App\Twig\Runtime\UserRequestExtensionRuntime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackActionsBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

readonly class SlackNotifier implements ChannelNotifierInterface
{
    private const CHANNELS = ['bugs' => 'C02UDN5PZFZ', 'features' => 'C05TWL4N3R9'];

    public function __construct(
        private ChatterInterface $chatter,
        private LoggerInterface $logger,
        private UserRequestExtensionRuntime $requestExtension,
    ) {
    }

    private function createSlackFeatureOptions(Feature $feature): SlackOptions
    {
        return $this->createSlackOptions(
            $feature->isDone() ? 'white_check_mark' : 'hourglass',
            $feature->isDone() ? 'Fonctionnalité développée' : 'Fonctionnalité demandée',
            $feature->getTitle() ?? '',
            $this->requestExtension->getShowPath($feature),
            self::CHANNELS['features']
        );
    }

    private function createSlackBugOptions(Bug $bug): SlackOptions
    {
        return $this->createSlackOptions(
            $bug->isDone() ? 'white_check_mark' : 'bug',
            $bug->isDone() ? 'Bug résolu' : 'Nouveau bug',
            $bug->getTitle() ?? '',
            $this->requestExtension->getShowPath($bug),
            self::CHANNELS['bugs']
        );
    }

    private function createSlackOptions(string $emoji, string $title, string $subtitle, string $path, string $recipient): SlackOptions
    {
        return (new SlackOptions())
            ->iconEmoji($emoji)
            ->username(sprintf('[MPB] %s', $title))
            ->block((new SlackSectionBlock())->text($subtitle))
            ->block(new SlackDividerBlock())
            ->block((new SlackActionsBlock())->button('Voir', $path, 'primary'))
            ->recipient($recipient);
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
        } elseif ($request->isFeature()) {
            /** @var Feature $request */
            return $this->buildFeatureMessage($request);
        }

        return null;
    }

    public function buildBugMessage(Bug $bug): ChatMessage
    {
        return (new ChatMessage($bug->isDone() ? 'Bug résolu' : 'Nouveau bug'))
            ->options($this->createSlackBugOptions($bug));
    }

    private function buildFeatureMessage(Feature $request): ChatMessage
    {
        return (new ChatMessage($request->isDone() ? 'Fonctionnalité développée' : 'Nouvelle fonctionnalité proposée'))
            ->options($this->createSlackFeatureOptions($request));
    }
}
