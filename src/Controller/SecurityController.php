<?php

namespace App\Controller;

use App\Form\LoginFormType;
use App\Service\SecurityService;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login", methods={"GET", "POST"})
     */
    public function login(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('bug_report_index');
        }
        $form = $this->createForm(LoginFormType::class);

        return $this->renderForm('security/login.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): Response
    {
        throw new \LogicException('This methods can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/sso_trigger", name="sso_trigger", methods={"GET"})
     */
    public function triggerSSOLogin(): RedirectResponse
    {
        $endPoint = $this->getParameter('loginEndpoint');
        $cliendId = $this->getParameter('clientId');
        $redirectUri = $this->getParameter('redirectUri');

        return $this->redirect("{$endPoint}&{$cliendId}&{$redirectUri}");
    }

    /**
     * @Route("/sso_login", name="redirect_uri", methods={"GET", "POST"})
     */
    public function SSOLogin(Request $request, SecurityService $service): RedirectResponse
    {
        if ($service->isSSOTokenValid($request->query->get('code'))) {
            return new RedirectResponse($this->generateUrl('bug_report_index'));
        } else {
            return new RedirectResponse($this->generateUrl('app_login'));
        }
    }
}
