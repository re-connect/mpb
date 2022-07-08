<?php

namespace App\Controller;

use App\Form\LoginFormType;
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
    public function redirectAction() : RedirectResponse
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('bug_report_index');
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function index(AuthenticationUtils $authenticationUtils) : Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        return $this->renderForm('security/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout() : Response
    {
        throw new \LogicException('This methods can be blank - it will be intercepted by the logout key on your firewall');
    }

    #[Route(path: '/sso_trigger', name: 'sso_trigger', methods: ['GET'])]
    public function triggerSSOLogin() : RedirectResponse
    {
        $endPoint = $this->getParameter('loginEndpoint');
        $cliendId = $this->getParameter('clientId');
        $redirectUri = $this->getParameter('redirectUri');
        return $this->redirect("{$endPoint}&{$cliendId}&{$redirectUri}");
    }

    #[Route(path: '/sso_login', name: 'redirect_uri', methods: ['GET', 'POST'])]
    public function SSOLogin(Request $request, SecurityService $service) : RedirectResponse
    {
        if ($service->isSSOTokenValid($request->query->get('code'))) {
            return new RedirectResponse($this->generateUrl('bug_report_index'));
        } else {
            return new RedirectResponse($this->generateUrl('app_login'));
        }
    }
}
