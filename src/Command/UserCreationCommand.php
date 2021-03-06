<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Add a short description for your command',
)]
class UserCreationCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly UserPasswordHasherInterface $hasher, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'email for the user to be created')
            ->addArgument('firstName', InputArgument::REQUIRED, 'first name for the user to be created')
            ->addArgument('lastName', InputArgument::REQUIRED, 'last name for the user to be created')
            ->addArgument('password', InputArgument::REQUIRED, 'password for the user to be created');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = (new User())
            ->setEmail($input->getArgument('email'))
            ->setFirstName($input->getArgument('firstName'))
            ->setLastName($input->getArgument('lastName'))
            ->setLastLogin(new \DateTime('now'))
            ->addRole('ROLE_ADMIN');
        $user->setPassword($this->hasher->hashPassword($user, $input->getArgument('password')));
        try {
            $this->em->persist($user);
            $this->em->flush();
            $io->success('Nouvel utilisateur créé');

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
    }
}
