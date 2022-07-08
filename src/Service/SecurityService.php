<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityService
{
    public function __construct(private readonly RequestStack $requestStack, private readonly EventDispatcherInterface $eventDispatcher, private readonly UserRepository $userRepository, private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function isSSOTokenValid(?string $token): bool
    {
        if (null !== $token) {
            $key = file_get_contents(dirname(__DIR__).'/../var/oauth/public.key');
            $decodedToken = (array) JWT::decode($token, new Key($key, 'RS256'));
            $user = $this->userRepository->findOneBy(['email' => $decodedToken['user_id']]);
            if (!$user) {
                throw new UserNotFoundException();
            }
            if (time() > (int) $decodedToken['expire_time']) {
                throw new AuthenticationException();
            }
            $this->authenticateUser($user);

            return true;
        }

        return false;
    }

    private function authenticateUser(User $user): void
    {
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token); // now the user is logged in
        // now dispatch the login event
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            $event = new InteractiveLoginEvent($request, $token);
            $this->eventDispatcher->dispatch($event, 'security.interactive_login');
        }
    }
}
