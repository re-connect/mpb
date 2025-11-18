<?php

namespace App\Tests\Command;

use App\Command\CleanDraftRequestCommand;
use App\Entity\Bug;
use App\Entity\Feature;
use App\Manager\UserRequestManager;
use App\Service\BugService;
use App\Service\FeatureService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CleanDraftRequestCommantTest extends KernelTestCase
{
    private BugService $mockBugService;
    private FeatureService $mockFeatureService;
    private UserRequestManager $mockUserRequestManager;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockBugService = $this->createMock(BugService::class);
        $this->mockFeatureService = $this->createMock(FeatureService::class);
        $this->mockUserRequestManager = $this->createMock(UserRequestManager::class);

        $command = new CleanDraftRequestCommand(
            $this->mockBugService,
            $this->mockFeatureService,
            $this->mockUserRequestManager
        );

        $this->commandTester = new CommandTester($command);
    }

    public function testAllDraftsCleaned(): void
    {
        $bugDraft = $this->createMock(Bug::class);
        $featureDraft = $this->createMock(Feature::class);

        $this->mockBugService->expects($this->once())->method('getDraftsToClean')->willReturn([$bugDraft]);
        $this->mockFeatureService->expects($this->once())->method('getDraftsToClean')->willReturn([$featureDraft]);

        $this->mockUserRequestManager
            ->expects($this->exactly(2))
            ->method('remove')
            ->withConsecutive([$bugDraft], [$featureDraft]);

        $this->commandTester->execute([]);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            sprintf('Deleting %d empty draft requests...', 2),
            $output
        );
        $this->assertStringContainsString(
            sprintf('%d empty draft request(s) successfully deleted.', 2),
            $output
        );
    }

    public function testNoDraftsToClean(): void
    {
        $this->mockBugService->expects($this->once())->method('getDraftsToClean')->willReturn([]);
        $this->mockFeatureService->expects($this->once())->method('getDraftsToClean')->willReturn([]);

        $this->mockUserRequestManager
            ->expects($this->never())
            ->method('remove');

        $this->commandTester->execute([]);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Nothing to clean.', $output);
    }
}
