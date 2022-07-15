<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppIconExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_icon', [$this, 'showAppIcon']),
        ];
    }

    public function showAppIcon($value): string
    {
        return match ($value) {
            'default' => 'fas fa-bug text-primary'
        };
    }
}
