<?php

namespace App\Command;

use App\Repository\BugRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:init-bug-lifecycle',
    description: 'This command purpose is to initialize existing bugs states values',
)]
class InitBugLifecycleCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly BugRepository $bugRepository, string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bugs = $this->bugRepository->findAll();

        foreach ($bugs as $bug) {
            if ($bug->isDone()) {
                $bug->setStatus('solved');
            } elseif (null !== $bug->getAssignee()) {
                $bug->setStatus('pending');
            } elseif (!$bug->isDraft()) {
                $bug->setStatus('pending_take_over');
            }
            $this->em->flush();
        }

        return Command::SUCCESS;
    }
}
