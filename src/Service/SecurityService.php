<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityService
{
    private RequestStack $requestStack;
    private EventDispatcherInterface $eventDispatcher;
    private UserRepository $userRepository;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function isSSOTokenValid(?string $token): bool
    {
        if (null !== $token) {
            $key = file_get_contents(dirname(__DIR__) . '/../var/oauth/public.key');
            $decodedToken = (array)JWT::decode($token, $key, ['RS256']);
            $user = $this->userRepository->findOneBy(['email' => $decodedToken['user_id']]);
            if (null === $user || time() > (int)$decodedToken['expire_time']) {
                throw new AuthenticationException();
            }
            $this->authenticateUser($user);

            return true;
        }

        return false;
    }

    private function authenticateUser(User $user)
    {
        if (!$user) {
            throw new UserNotFoundException();
        }
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token); //now the user is logged in
        //now dispatch the login event
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            $event = new InteractiveLoginEvent($request, $token);
            $this->eventDispatcher->dispatch($event, 'security.interactive_login');
        }
    }
}