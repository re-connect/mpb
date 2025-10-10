<?php

namespace App\Command;

use App\Entity\Bug;
use App\Entity\Feature;
use App\Entity\UserRequest;
use App\Manager\BugManager;
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
    private BugService $bugService;
    private FeatureService $featureService;

    private UserRequestManager $userRequestManager;

    public function __construct(
        BugService $bugService,
        FeatureService $featureService,
        UserRequestManager $userRequestManager,

    ) {
        parent::__construct();

        $this->bugService = $bugService;
        $this->featureService = $featureService;
        $this->userRequestManager = $userRequestManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $draftUserRequests = array_merge(
            $this->bugService->getDraftsToClean(),
            $this->featureService->getDraftsToClean()
        );

        $count = count($draftUserRequests);
        if ($count === 0) {
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
