<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BugLifecycleExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('bug_lifecycle_transition_icon', $this->getTransitionIcon(...)),
            new TwigFunction('bug_lifecycle_state_icon', $this->getStateIcon(...)),
            new TwigFunction('bug_lifecycle_state_color', $this->getStateColor(...)),
        ];
    }

    public function getTransitionIcon(?string $transition): string
    {
        return match ($transition) {
            'take_over' => 'truck-fast',
            'unprioritize' => 'clock',
            'dismiss' => 'bug-slash',
            'solve' => 'check',
            default => ''
        };
    }

    public function getStateIcon(?string $state): string
    {
        return match ($state) {
            'pending_take_over' => 'hourglass-1',
            'pending' => 'truck-fast',
            'low_priority' => 'clock',
            'not_a_bug' => 'bug-slash',
            'solved' => 'check',
            default => ''
        };
    }

    public function getStateColor(?string $state): string
    {
        return match ($state) {
            'pending_take_over' => '#FFA41E',
            'pending' => '#3498db',
            'low_priority' => '#754eb1',
            'not_a_bug' => '#606060',
            'solved' => '#28AD7A',
            default => ''
        };
    }
}
