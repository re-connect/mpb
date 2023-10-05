<?php

namespace App\Tests\Controller;

use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

abstract class AbstractControllerTest extends WebTestCase
{
    use Factories;
    protected static TranslatorInterface $translator;

    public static function setUpBeforeClass(): void
    {
        self::ensureKernelShutdown();
        $container = self::createClient()->getContainer();
        self::$translator = $container->get(TranslatorInterface::class);
    }

    public function assertRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        self::ensureKernelShutdown();
        $clientTest = static::createClient();

        if ($userEmail) {
            $user = UserFactory::findOrCreate(['email' => $userEmail])->object();
            $clientTest->loginUser($user);
        }

        $clientTest->request($method, $url);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
        if ($expectedRedirect) {
            $this->assertResponseRedirects($expectedRedirect);
        }
    }

    /**
     * @param array<string, string> $values
     */
    public function assertFormIsValid(string $url, string $formSubmit, array $values, ?string $email, ?string $redirectUrl): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        if ($email) {
            $user = UserFactory::findOrCreate(['email' => $email])->object();
            $client->loginUser($user);
        }
        $crawler = $client->request('GET', $url);
        $form = $crawler->selectButton(self::$translator->trans($formSubmit))->form();
        $form->setValues($values);
        $client->submit($form);

        if ($redirectUrl) {
            $this->assertResponseStatusCodeSame(302);
            $this->assertResponseRedirects($redirectUrl);
        } else {
            $this->assertResponseStatusCodeSame(200);
        }
    }

    /**
     * @param array<string, string> $values
     * @param array<int, array<string, string|array<string, string>>> $errors
     */
    public function assertFormIsNotValid(string $url, string $route, string $formSubmit, array $values, array $errors, ?string $email, string $alternateSelector = null): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        if ($email) {
            $user = UserFactory::findOrCreate(['email' => $email])->object();
            $client->loginUser($user);
        }
        $crawler = $client->request('GET', $url);
        $form = $crawler->selectButton(self::$translator->trans($formSubmit))->form();
        $form->setValues($values);
        $client->submit($form);

        foreach ($errors as $error) {
            $this->assertSelectorTextContains($alternateSelector ?? 'span.form-error-message', self::$translator->trans($error['message'], $error['params'], 'validators'));
        }
        $this->assertResponseStatusCodeSame(422);
        $this->assertRouteSame($route);
    }
}
