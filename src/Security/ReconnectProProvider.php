<?php

namespace App\Security;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class ReconnectProProvider extends AbstractProvider
{
    private string $baseUrl = 'https://pro.reconnect.fr';

    private function getProviderUrl(string $path): string
    {
        return sprintf('%s%s', $this->baseUrl, $path);
    }

    public function getBaseAuthorizationUrl(): string
    {
        return $this->getProviderUrl('/authorize');
    }

    /**
     * @param string[] $params
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->getProviderUrl('/token');
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->getProviderUrl('/resource');
    }

    /**
     * @return string[]
     */
    protected function getDefaultScopes(): array
    {
        return [];
    }

    /**
     * @param string[]|string $data
     */
    protected function checkResponse(ResponseInterface $response, mixed $data): void
    {
    }

    /**
     * @param array<string> $response
     *
     * @throws \Exception
     */
    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        throw new \Exception('Trying to create ressource owner, this should not be reached');
    }
}
