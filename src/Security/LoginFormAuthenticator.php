<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    const PROVIDED_EMAIL = 'providedEmail';
    private RequestStack $requestStack;
    private UserRepository $userRepository;
    private UrlGeneratorInterface $urlGenerator;
    private FlashBagInterface $flashbagInterface;

    public function __construct(RequestStack $requestStack, UserRepository $userRepository, UrlGeneratorInterface $urlGenerator, FlashBagInterface $flashbagInterface)
    {
        $this->requestStack = $requestStack;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->flashbagInterface = $flashbagInterface;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'app_login'
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $providedEmail = $request->request->get('login_form')['email'];
        $foundUser = $this->userRepository->findOneBy(['email' => $providedEmail]);
        $this->requestStack->getSession()->set(self::PROVIDED_EMAIL, $providedEmail);
        if (!$foundUser) {
            throw new UserNotFoundException;
        }

        return new Passport(new UserBadge($providedEmail), new PasswordCredentials($request->request->get('login_form')['password']), [
            new CsrfTokenBadge('login_form', $request->request->get('login_form')['_token']),
            new RememberMeBadge()
        ]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        $this->requestStack->getSession()->remove(self::PROVIDED_EMAIL);

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        $this->flashbagInterface->set('danger', $exception->getMessage());

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}