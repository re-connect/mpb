<?php

namespace App\Twig\Runtime;

use App\Entity\UserRequest;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\RuntimeExtensionInterface;

readonly class UserRequestExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function getMarkDonePath(UserRequest $request): string
    {
        if ($request->isBug()) {
            return $this->router->generate('bug_mark_done', ['id' => $request->getId()]);
        } elseif ($request->isFeature()) {
            return $this->router->generate('feature_mark_done', ['id' => $request->getId()]);
        }

        return '';
    }

    public function getVotePath(UserRequest $request): string
    {
        if ($request->isBug()) {
            return $this->router->generate('bug_vote', ['id' => $request->getId()]);
        } elseif ($request->isFeature()) {
            return $this->router->generate('feature_vote', ['id' => $request->getId()]);
        }

        return '';
    }
}
