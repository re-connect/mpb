<?php

namespace App\Command;

use App\Service\Notifier\EmailNotifier;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:check-disk-capacity',
    description: 'Send email alert to tech team if server disk capacity percentage exceeds alert threshold',
)]
class CheckDiskCapacityCommand extends Command
{
    private const CAPACITY_ALERT_THRESHOLD = 80;

    public function __construct(
        private readonly EmailNotifier $notifier,
        private readonly string $techTeamEmail,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = Process::fromShellCommandline("df -h | grep $(findmnt -T $(pwd) -o SOURCE -n) | awk '{print $5}'");
        $process->run();
        $capacityPercentage = $process->getOutput();

        if ($capacityPercentage && self::CAPACITY_ALERT_THRESHOLD <= intval($capacityPercentage)) {
            $this->notifier->sendEmail(
                'Alerte capacité disque dur serveur',
                sprintf(
                    'La capacité actuelle du disque dur du serveur book est de %s',
                    $capacityPercentage,
                ),
                $this->techTeamEmail,
            );
        }

        return Command::SUCCESS;
    }
}
