<?php

namespace App\Tests\Service\Notifier;

use App\DataFixtures\BugFixtures;
use App\DataFixtures\FeatureFixtures;
use App\Tests\Factory\BugFactory;
use App\Tests\Factory\FeatureFactory;
use Symfony\Component\Mime\RawMessage;

class EmailNotifierTest extends AbstractNotifierTest
{
    public function testEmailIsSendWithFeature(): void
    {
        $feature = FeatureFactory::find(['title' => FeatureFixtures::DONE_WITH_VOTE_FROM_TEAM_USER])->object();
        self::assertCount(1, $feature->getVotes());
        self::assertTrue($feature->isDone());

        $recipients = sprintf(
            '%s, %s',
            $feature->getUser()->getEmail(),
            $feature->getVotes()[0]->getVoter()->getEmail(),
        );

        $this->emailNotifier->notify($feature);

        $this->assertEmail(
            $this->getMailerMessage() ?? new RawMessage(''),
            $recipients,
            [
                "La demande d'amélioration suivante a été traitée",
                $this->urlGenerator->generate('feature_show', ['id' => $feature->getId()], $this->urlGenerator::ABSOLUTE_URL),
                'Pensez à prévenir les personnes l’ayant remontée qu’elle a été traitée',
            ],
        );
    }

    public function testEmailIsSendWithBug(): void
    {
        $bug = BugFactory::createOne(['done' => true])->object();
        $this->emailNotifier->notify($bug);
        $this->assertEmail(
            $this->getMailerMessage() ?? new RawMessage(''),
            $bug->getUser()->getEmail(),
            [
                'Le bug suivant a été résolu',
                $this->urlGenerator->generate('bug_show', ['id' => $bug->getId()], $this->urlGenerator::ABSOLUTE_URL),
            ],
        );
    }

    /**
     * @dataProvider provideTestEmailSentWhenBugIsDone
     */
    public function testEmailSentWhenBugIsDone(string $bugTitle, int $emailCount): void
    {
        $bug = BugFactory::find(['title' => $bugTitle])->object();
        $this->emailNotifier->notify($bug);
        self::assertEmailCount($emailCount);
    }

    public function provideTestEmailSentWhenBugIsDone(): \Generator
    {
        yield 'Should not send email if bug is not done' => [BugFixtures::BUG_NOT_DONE_FROM_TEAM_USER, 0];
        yield 'Should send email if bug is done' => [BugFixtures::BUG_DONE_FROM_TEAM_USER, 1];
    }

    /**
     * @dataProvider provideTestEmailSentWhenFeatureIsDone
     */
    public function testEmailSentWhenFeatureIsDone(string $featureTitle, int $emailCount): void
    {
        $bug = FeatureFactory::find(['title' => $featureTitle])->object();
        $this->emailNotifier->notify($bug);
        self::assertEmailCount($emailCount);
    }

    public function provideTestEmailSentWhenFeatureIsDone(): \Generator
    {
        yield 'Should not send email if feature is not done' => [FeatureFixtures::NOT_DONE_FROM_TEAM_USER, 0];
        yield 'Should send email if feature is done' => [FeatureFixtures::DONE_WITH_VOTE_FROM_TEAM_USER, 1];
    }
}
