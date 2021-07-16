<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreationCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    protected static $defaultDescription = 'Add a short description for your command';
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher, string $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->hasher = $hasher;
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
            ->setRole('ROLE_USER');
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
