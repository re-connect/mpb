<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    protected function getUser(): ?User
    {
        $user = parent::getUser();
        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    /** @param array<string, string> $params */
    protected function refreshOrRedirect(string $route, array $params = []): RedirectResponse
    {
        $referer = $this->requestStack->getMainRequest()?->headers->get('referer');

        return $this->redirect($referer ?? $this->generateUrl($route, $params));
    }
}
