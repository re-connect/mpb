<?php

namespace App\Controller;

use App\Service\SecurityService;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
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
        $redirectRoute = $this->getUser() ? 'bugs_list' : 'app_login';

        return $this->redirectToRoute($redirectRoute);
    }

    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function loginForm(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        throw new \LogicException('This methods can be blank - it will be intercepted by the logout key on your firewall');
    }

    #[Route(path: '/reconnect-pro-login-trigger', name: 'reconnect_pro_login_trigger', methods: ['GET'])]
    public function reconnectProLoginTrigger(OAuth2Client $client): mixed
    {
        return $client->getOAuth2Provider()->authorize();
    }

    /** @throws \Exception */
    #[Route(path: '/reconnect-pro-check', name: 'reconnect_pro_login_check', methods: ['GET'])]
    public function reconnectProLoginCheck(Request $request, SecurityService $service): Response
    {
        return $service->authenticateUserFromReconnectPro($request);
    }

    #[Route(path: '/google-login-trigger', name: 'google_login_trigger', methods: ['GET'])]
    public function googleLoginTrigger(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry->getClient('google')->redirect([], []);
    }

    #[Route(path: '/google-check', name: 'google_login_check', methods: ['GET'])]
    public function googleLoginCheck(Request $request, SecurityService $service): Response
    {
        return $service->authenticateUserFromGoogle($request);
    }

    #[Route(path: '/slack-chat', name: 'slack_chat', methods: ['POST'])]
    public function slackChat(): Response
    {
        return new Response(null, Response::HTTP_OK);
    }
}
