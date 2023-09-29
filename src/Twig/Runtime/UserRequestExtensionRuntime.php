<?php

namespace App\Twig\Runtime;

use App\Entity\UserRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

readonly class UserRequestExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function getMarkDonePath(UserRequest $request): string
    {
        if ($request->isBug()) {
            return $this->urlGenerator->generate('bug_mark_done', ['id' => $request->getId()]);
        } elseif ($request->isFeature()) {
            return $this->urlGenerator->generate('feature_mark_done', ['id' => $request->getId()]);
        }

        return '';
    }

    public function getShowPath(UserRequest $request): string
    {
        if ($request->isBug()) {
            return $this->urlGenerator->generate('bug_show', ['id' => $request->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        } elseif ($request->isFeature()) {
            return $this->urlGenerator->generate('feature_show', ['id' => $request->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return '';
    }

    public function getVotePath(UserRequest $request): string
    {
        if ($request->isBug()) {
            return $this->urlGenerator->generate('bug_vote', ['id' => $request->getId()]);
        } elseif ($request->isFeature()) {
            return $this->urlGenerator->generate('feature_vote', ['id' => $request->getId()]);
        }

        return '';
    }

    public function getConvertPath(UserRequest $request): string
    {
        if ($request->isBug()) {
            return $this->urlGenerator->generate('bug_convert_to_feature', ['id' => $request->getId()]);
        }

        return '';
    }

    public function getTakeOverPath(UserRequest $request): string
    {
        if ($request->isBug()) {
            return $this->urlGenerator->generate('bug_take_over', ['id' => $request->getId()]);
        }

        return '';
    }
}
