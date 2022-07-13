<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class SecurityService
{
    public function __construct(
        private readonly OAuth2Client $client,
        private readonly UserAuthenticatorInterface $authenticator,
        private readonly FormLoginAuthenticator $formLoginAuthenticator,
        private readonly UserRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly RouterInterface $router,
        private readonly string $reconnectProJwtPublicKey,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function authenticateUserFromReconnectPro(Request $request): string
    {
        try {
            $token = $this->client->getAccessToken();
        } catch (\Exception) {
            return $this->router->generate('app_home');
        }

        $key = file_get_contents($this->reconnectProJwtPublicKey);
        if (!$key) {
            throw new \Exception(sprintf('Could not find key at path %s', $this->reconnectProJwtPublicKey));
        }

        JWT::$leeway = 60;
        $decoded = (array) JWT::decode($token->getToken(), new Key($key, 'RS256'));
        $email = $decoded['sub'] ?? null;

        if ($email) {
            $user = $this->repository->findOneBy(['email' => $email]);
            if (!$user) {
                $user = (new User())->setEmail($email)->setPassword('')->addRole('ROLE_USER');
                if (str_ends_with((string) $email, '@reconnect.fr')) {
                    $user->addRole('ROLE_TEAM');
                }
                $this->em->persist($user);
                $this->em->flush();
            }
            $this->authenticator->authenticateUser($user, $this->formLoginAuthenticator, $request);
        }

        return $this->router->generate('app_home');
    }
}
