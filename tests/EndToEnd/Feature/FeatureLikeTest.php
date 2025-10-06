<?php

namespace App\Tests\EndToEnd\Feature;

use App\Entity\Feature;
use App\Tests\EndToEnd\AbstractBrowserKitEndToEndTest;
use App\Tests\Factory\FeatureFactory;

class FeatureLikeTest extends AbstractBrowserKitEndToEndTest
{
    private Feature $feature;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var Feature $feature */
        $feature = FeatureFactory::createOne()->object();
        $this->feature = $feature;

        $this->user = $this->loginUser();
    }

    private function clickVoteLink(): void
    {
        $crawler = $this->client->getCrawler();
        $link = $crawler->filter("a[href='/features/{$this->feature->getId()}/vote']")->link();
        $this->client->click($link);
    }

    private function clickAddComment(string $content = 'Test'): void
    {
        $crawler = $this->visit('GET', "/features/{$this->feature->getId()}/add-comment");

        $form = $crawler->selectButton('Ajouter un commentaire')->form([
            'comment[content]' => $content,
        ]);

        $this->client->submit($form);
    }

    private function assertCounter(int $counter): void
    {
        $this->assertSelectorTextContains(
            "a[href='/features/{$this->feature->getId()}/vote'] span",
            "($counter)"
        );
    }

    public function testIncrementLikeOnFeatureListPage(): void
    {
        $this->visit('GET', '/features/list');

        $this->assertCounter(0);
        $this->clickVoteLink();
        $this->assertCounter(1);
    }

    public function testIncrementLikeAndDecrementOnFeatureListPage(): void
    {
        $this->visit('GET', '/features/list');

        $this->assertCounter(0);
        $this->clickVoteLink();
        $this->assertCounter(1);
        $this->clickVoteLink();
        $this->assertCounter(0);
    }

    public function testIncrementLikeOnFeaturePage(): void
    {
        $this->visit('GET', "/features/{$this->feature->getId()}");

        $this->assertCounter(0);
        $this->clickVoteLink();
        $this->assertCounter(1);
    }

    public function testIncrementLikeAndDecrementOnFeaturePage(): void
    {
        $this->visit('GET', "/features/{$this->feature->getId()}");

        $this->assertCounter(0);
        $this->clickVoteLink();
        $this->assertCounter(1);
        $this->clickVoteLink();
        $this->assertCounter(0);
    }

    public function testIncrementLikeOnCommentAdded(): void
    {
        $this->clickAddComment();

        $this->visit('GET', "/features/{$this->feature->getId()}");
        $this->assertCounter(1);

        $this->visit('GET', '/features/list');
        $this->assertCounter(1);
    }

    public function testIncrementLikeWhenCommentsAddedBeforeVote(): void
    {
        $this->clickAddComment();
        $this->clickAddComment();

        $this->visit('GET', "/features/{$this->feature->getId()}");
        $this->assertCounter(2);
        $this->clickVoteLink();
        $this->assertCounter(3);

        $this->visit('GET', '/features/list');
        $this->assertCounter(3);
    }

    public function testIncrementLikeWhenVoteAddedBeforeComments(): void
    {
        $this->visit('GET', "/features/{$this->feature->getId()}");
        $this->clickVoteLink();
        $this->clickAddComment();
        $this->clickAddComment();

        $this->visit('GET', "/features/{$this->feature->getId()}");
        $this->assertCounter(3);

        $this->visit('GET', '/features/list');
        $this->assertCounter(3);
    }

    public function testDecrementLikeAfterCommentsAdded(): void
    {
        $this->visit('GET', "/features/{$this->feature->getId()}");
        $this->clickVoteLink();
        $this->clickAddComment();
        $this->clickAddComment();

        $this->visit('GET', "/features/{$this->feature->getId()}");
        $this->assertCounter(3);
        $this->clickVoteLink();
        $this->assertCounter(2);

        $this->visit('GET', '/features/list');
        $this->assertCounter(2);
    }
}
