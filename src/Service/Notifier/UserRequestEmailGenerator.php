<?php

namespace App\Service\Notifier;

use App\Entity\UserRequest;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class UserRequestEmailGenerator
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function generate(UserRequest $request): ?Email
    {
        return (new Email())
            ->from('contact@reconnect.fr')
            ->to(...$this->getRecipients($request))
            ->subject($this->getSubject($request))
            ->html($this->getContent($request));
    }

    private function getSubject(UserRequest $request): string
    {
        if ($request->isBug()) {
            return '[MPB] Bug résolu';
        } elseif ($request->isFeature()) {
            return '[MPB] Demande d’amélioration traitée';
        }

        return '';
    }

    private function getContent(UserRequest $request): string
    {
        $baseContent = '<p>%s<br/><br/>%s : %s.</p>';
        $requestId = $request->getId();
        $title = $request->getTitle();

        if ($request->isBug()) {
            return sprintf(
                $baseContent,
                'Le bug suivant a été résolu',
                $title,
                $this->urlGenerator->generate('bug_show', ['id' => $requestId], UrlGeneratorInterface::ABSOLUTE_URL),
            );
        } elseif ($request->isFeature()) {
            $content = sprintf(
                $baseContent,
                "La demande d'amélioration suivante a été traitée",
                $title,
                $this->urlGenerator->generate('feature_show', ['id' => $requestId], UrlGeneratorInterface::ABSOLUTE_URL),
            );

            return sprintf('%s%s', $content, 'Pensez à prévenir les personnes l’ayant remontée qu’elle a été traitée');
        }

        return '';
    }

    /**
     * @return string[]
     */
    private function getRecipients(UserRequest $request): array
    {
        $creatorEmail = $request->getUser()?->getEmail() ?? '';

        if ($request->isBug()) {
            return [$creatorEmail];
        } elseif ($request->isFeature()) {
            return [$creatorEmail, ...$request->getVotersEmail()->toArray()];
        }

        return [];
    }
}
