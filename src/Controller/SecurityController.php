<?php

namespace App\Controller;

use App\Service\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_home')]
    public function redirectAction(): RedirectResponse
    {
        $redirectRoute = $this->getUser() ? 'bug_report_index' : 'app_login';

        return $this->redirectToRoute($redirectRoute);
    }

    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->renderForm('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        throw new \LogicException('This methods can be blank - it will be intercepted by the logout key on your firewall');
    }

    #[Route(path: '/sso_trigger', name: 'sso_trigger', methods: ['GET'])]
    public function triggerSSOLogin(string $loginEndpoint, string $clientId, string $redirectUri): RedirectResponse
    {
        return $this->redirect("{$loginEndpoint}&{$clientId}&{$redirectUri}");
    }

    #[Route(path: '/sso_login', name: 'redirect_uri', methods: ['GET', 'POST'])]
    public function SSOLogin(Request $request, SecurityService $service): RedirectResponse
    {
        $redirectUrl = $service->isSSOTokenValid($request->query->get('code'))
            ? $this->generateUrl('bug_report_index')
            : $this->generateUrl('app_login');

        return new RedirectResponse($redirectUrl);
    }
}
