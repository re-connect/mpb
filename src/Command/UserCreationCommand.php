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
        $email = $input->getArgument('email');
        $firstname = $input->getArgument('firstName');
        $lastname = $input->getArgument('lastName');
        $password = $input->getArgument('password');

        if (!is_string($email) || !is_string($firstname) || !is_string($lastname) || !is_string($password)) {
            return Command::FAILURE;
        }

        $io = new SymfonyStyle($input, $output);
        $user = (new User())
            ->setEmail($email)
            ->setFirstName($firstname)
            ->setLastName($lastname)
            ->setLastLogin(new \DateTime('now'))
            ->addRole('ROLE_ADMIN');
        $user->setPassword($this->hasher->hashPassword($user, $password));
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
