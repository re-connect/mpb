<?php

namespace App\Command;

use App\Manager\UserRequestManager;
use App\Service\BugService;
use App\Service\FeatureService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clean-draft-request',
    description: 'Clean draft requests',
)]
class CleanDraftRequestCommand extends Command
{
    public function __construct(
        private readonly BugService $bugService,
        private readonly FeatureService $featureService,
        private readonly UserRequestManager $userRequestManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $draftUserRequests = [...$this->bugService->getDraftsToClean(), ...$this->featureService->getDraftsToClean()];

        $count = count($draftUserRequests);
        if (0 === $count) {
            $io->success('Nothing to clean.');

            return Command::SUCCESS;
        }

        $io->section(sprintf('Deleting %d empty draft requests...', $count));

        $io->progressStart($count);

        foreach ($draftUserRequests as $draft) {
            $this->userRequestManager->remove($draft);
            $io->progressAdvance();
        }

        $io->progressFinish();

        $io->success(sprintf('%d empty draft request(s) successfully deleted.', $count));

        return Command::SUCCESS;
    }
}
