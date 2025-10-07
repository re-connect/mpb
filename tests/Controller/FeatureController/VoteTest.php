<?php

namespace App\Tests\Controller\FeatureController;

use App\DataFixtures\UserFixtures;
use App\Entity\Feature;
use App\Entity\User;
use App\Tests\Controller\AbstractControllerTest;
use App\Tests\Controller\TestRouteInterface;
use App\Tests\Factory\FeatureFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\BrowserKit\AbstractBrowser;

class VoteTest extends AbstractControllerTest implements TestRouteInterface
{
    private const VOTE_URL = '/features/%s/vote';
    private const LIST_URL = '/features/list';
    private const FEATURE_URL = '/features/%s';

    /** @dataProvider provideTestRoute */
    public function testRoute(string $url, int $expectedStatusCode, string $userEmail = null, string $expectedRedirect = null, string $method = 'GET'): void
    {
        $feature = FeatureFactory::randomOrCreate()->object();
        $url = sprintf($url, $feature->getId());
        $this->assertRoute($url, $expectedStatusCode, $userEmail, $expectedRedirect, $method);
    }

    public function provideTestRoute(): \Generator
    {
        yield '[Vote] Should redirect to login when not connected' => [self::VOTE_URL, 302, null, 'http://localhost/login'];
        yield '[Vote] Should redirect to list when connected as user' => [self::VOTE_URL, 302, UserFixtures::USER_MAIL, self::LIST_URL];
        yield '[Vote] Should redirect to list when connected as team member' => [self::VOTE_URL, 302, UserFixtures::TEAM_USER_MAIL, self::LIST_URL];
        yield '[Vote] Should redirect to list when connected as tech team member' => [self::VOTE_URL, 302, UserFixtures::TECH_TEAM_USER_MAIL, self::LIST_URL];
        yield '[Vote] Should redirect to list when connected as admin' => [self::VOTE_URL, 302, UserFixtures::ADMIN_USER_MAIL, self::LIST_URL];
    }

    private function getUser(string $email): User
    {
        /** @var User $user */
        $user = UserFactory::findOrCreate(['email' => $email])->object();

        return $user;
    }

    private function assertLikeCounter(Feature $feature, int $counter): void
    {
        $this->assertSelectorTextContains(
            "a[href='/features/{$feature->getId()}/vote'] span",
            "($counter)"
        );
    }

    private function clickVote(Feature $feature, AbstractBrowser $client): void
    {
        $crawler = $client->getCrawler();
        $link = $crawler->filter("a[href='/features/{$feature->getId()}/vote']")->link();
        $client->click($link);
    }

    private function clickAddComment(Feature $feature, AbstractBrowser $client, string $content = 'Test'): void
    {
        $crawler = $client->request('GET', "/features/{$feature->getId()}/add-comment");

        $form = $crawler->selectButton('Ajouter un commentaire')->form([
            'comment[content]' => $content,
        ]);

        $client->submit($form);
    }

    public function testIncrementLikeOnListPage(): void
    {
        $clientTest = static::createClient();
        $clientTest->followRedirects();

        $clientTest->loginUser($this->getUser(USerFixtures::USER_MAIL));

        /** @var Feature $feature */
        $feature = FeatureFactory::createOne()->object();

        $clientTest->request('GET', self::LIST_URL);
        $this->clickVote($feature, $clientTest);
        $this->assertLikeCounter($feature, 1);
    }

    public function testIncrementLikeAndDecrementOnFeatureListPage(): void
    {
        $clientTest = static::createClient();
        $clientTest->followRedirects();

        $clientTest->loginUser($this->getUser(USerFixtures::USER_MAIL));

        /** @var Feature $feature */
        $feature = FeatureFactory::createOne()->object();

        $clientTest->request('GET', self::LIST_URL);

        $this->assertLikeCounter($feature, 0);
        $this->clickVote($feature, $clientTest);
        $this->assertLikeCounter($feature, 1);
        $this->clickVote($feature, $clientTest);
        $this->assertLikeCounter($feature, 0);
    }

    public function testIncrementLikeOnFeaturePage(): void
    {
        $clientTest = static::createClient();
        $clientTest->followRedirects();

        $clientTest->loginUser($this->getUser(USerFixtures::USER_MAIL));

        /** @var Feature $feature */
        $feature = FeatureFactory::createOne()->object();

        $clientTest->request('GET', sprintf(self::FEATURE_URL, $feature->getId()));

        $this->assertLikeCounter($feature, 0);
        $this->clickVote($feature, $clientTest);
        $this->assertLikeCounter($feature, 1);
    }

    public function testIncrementLikeAndDecrementOnFeaturePage(): void
    {
        $clientTest = static::createClient();
        $clientTest->followRedirects();

        $clientTest->loginUser($this->getUser(USerFixtures::USER_MAIL));

        /** @var Feature $feature */
        $feature = FeatureFactory::createOne()->object();

        $clientTest->request('GET', sprintf(self::FEATURE_URL, $feature->getId()));

        $this->assertLikeCounter($feature, 0);
        $this->clickVote($feature, $clientTest);
        $this->assertLikeCounter($feature, 1);
        $this->clickVote($feature, $clientTest);
        $this->assertLikeCounter($feature, 0);
    }

    public function testIncrementLikeOnCommentAdded(): void
    {
        $clientTest = static::createClient();
        $clientTest->followRedirects();

        $clientTest->loginUser($this->getUser(USerFixtures::USER_MAIL));

        /** @var Feature $feature */
        $feature = FeatureFactory::createOne()->object();

        $this->clickAddComment($feature, $clientTest);

        $clientTest->request('GET', sprintf(self::FEATURE_URL, $feature->getId()));
        $this->assertLikeCounter($feature, 1);

        $clientTest->request('GET', self::LIST_URL);
        $this->assertLikeCounter($feature, 1);
    }

    public function testIncrementLikeWhenCommentsAddedBeforeVote(): void
    {
        $clientTest = static::createClient();
        $clientTest->followRedirects();

        $clientTest->loginUser($this->getUser(USerFixtures::USER_MAIL));

        /** @var Feature $feature */
        $feature = FeatureFactory::createOne()->object();

        $this->clickAddComment($feature, $clientTest);
        $this->clickAddComment($feature, $clientTest);

        $clientTest->request('GET', sprintf(self::FEATURE_URL, $feature->getId()));
        $this->assertLikeCounter($feature, 2);
        $this->clickVote($feature, $clientTest);
        $this->assertLikeCounter($feature, 3);

        $clientTest->request('GET', self::LIST_URL);
        $this->assertLikeCounter($feature, 3);
    }

    public function testIncrementLikeWhenVoteAddedBeforeComments(): void
    {
        $clientTest = static::createClient();
        $clientTest->followRedirects();

        $clientTest->loginUser($this->getUser(USerFixtures::USER_MAIL));

        /** @var Feature $feature */
        $feature = FeatureFactory::createOne()->object();

        $clientTest->request('GET', sprintf(self::FEATURE_URL, $feature->getId()));
        $this->clickVote($feature, $clientTest);
        $this->assertLikeCounter($feature, 1);
        $this->clickAddComment($feature, $clientTest);
        $this->clickAddComment($feature, $clientTest);

        $clientTest->request('GET', sprintf(self::FEATURE_URL, $feature->getId()));
        $this->assertLikeCounter($feature, 3);

        $clientTest->request('GET', self::LIST_URL);
        $this->assertLikeCounter($feature, 3);
    }

    public function testDecrementLikeAfterCommentsAdded(): void
    {
        $clientTest = static::createClient();
        $clientTest->followRedirects();

        $clientTest->loginUser($this->getUser(USerFixtures::USER_MAIL));

        /** @var Feature $feature */
        $feature = FeatureFactory::createOne()->object();

        $clientTest->request('GET', sprintf(self::FEATURE_URL, $feature->getId()));
        $this->clickVote($feature, $clientTest);
        $this->clickAddComment($feature, $clientTest);
        $this->clickAddComment($feature, $clientTest);

        $clientTest->request('GET', sprintf(self::FEATURE_URL, $feature->getId()));
        $this->assertLikeCounter($feature, 3);
        $this->clickVote($feature, $clientTest);
        $this->assertLikeCounter($feature, 2);

        $clientTest->request('GET', self::LIST_URL);
        $this->assertLikeCounter($feature, 2);
    }
}
