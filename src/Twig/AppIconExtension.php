<?php

namespace App\Twig;

use App\Entity\Application;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppIconExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_icon', $this->showAppIcon(...), [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    /**
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public function showAppIcon(Environment $environment, ?Application $application, ?bool $isActive = false): string
    {
        if (!$application) {
            return '';
        }

        return $environment->render(name: 'bug/_application_icon.html.twig', context: [
            'application' => $application,
            'isActive' => $isActive,
        ]);
    }
}
