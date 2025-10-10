<?php

namespace App\Command;

use App\Entity\Bug;
use App\Entity\Feature;
use Doctrine\ORM\EntityManagerInterface;
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
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $bugRepository = $this->entityManager->getRepository(Bug::class);
        $featureRepository = $this->entityManager->getRepository(Feature::class);

        $draftUserRequests = array_merge(
            $bugRepository->findDraftsToClean(),
            $featureRepository->findDraftsToClean()
        );

        $count = count($draftUserRequests);
        if ($count === 0) {
            $io->success('Nothing to clean.');
            return Command::SUCCESS;
        }

        $io->section(sprintf('Deleting %d empty draft requests...', $count));

        $io->progressStart($count);

        foreach ($draftUserRequests as $draft) {
            $this->entityManager->remove($draft);
            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $io->progressFinish();

        $io->success(sprintf('%d empty draft request(s) successfully deleted.', $count));

        return Command::SUCCESS;
    }
}
