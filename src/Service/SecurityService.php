<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityService
{
    use TargetPathTrait;

    public function __construct(
        private readonly OAuth2Client $client,
        private readonly UserAuthenticatorInterface $authenticator,
        private readonly FormLoginAuthenticator $formLoginAuthenticator,
        private readonly UserRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly RouterInterface $router,
        private readonly ClientRegistry $registry,
        private readonly string $reconnectProJwtPublicKey,
    ) {
    }

    public function authenticateUserFromGoogle(Request $request): Response
    {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient $client */
        $client = $this->registry->getClient('google');

        try {
            /** @var \League\OAuth2\Client\Provider\GoogleUser $user */
            $user = $client->fetchUser();
            $email = $user->getEmail();

            return $this->authenticateOrCreateUser($email, $request);
        } catch (\Exception) {
            throw new AccessDeniedException();
        }
    }

    /** @throws \Exception */
    public function authenticateUserFromReconnectPro(Request $request): Response
    {
        try {
            $token = $this->client->getAccessToken();
        } catch (\Exception) {
            return new RedirectResponse($this->router->generate('app_home'));
        }

        $key = file_get_contents($this->reconnectProJwtPublicKey);
        if (!$key) {
            throw new \Exception(sprintf('Could not find key at path %s', $this->reconnectProJwtPublicKey));
        }

        JWT::$leeway = 60;
        $decoded = (array) JWT::decode($token->getToken(), new Key($key, 'RS256'));
        $email = $decoded['sub'] ?? null;

        return $this->authenticateOrCreateUser($email, $request);
    }

    public function authenticateOrCreateUser(?string $email, Request $request): Response
    {
        $redirectPath = $this->getTargetPath($request->getSession(), 'main') ?? $this->router->generate('app_home');

        if ($email) {
            $user = $this->repository->findOneBy(['email' => $email]);
            if (!$user) {
                $user = (new User())->setEmail($email)->setPassword('')->addRole('ROLE_USER');
                if (str_ends_with($email, '@reconnect.fr')) {
                    $user->addRole('ROLE_TEAM');
                }
                $this->em->persist($user);
                $this->em->flush();
            }

            $this->authenticator->authenticateUser($user, $this->formLoginAuthenticator, $request);
        }

        return new RedirectResponse($redirectPath);
    }
}
