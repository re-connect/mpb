<?php

namespace App\Command;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CategoryCreationCommand extends Command
{
    protected static $defaultName = 'app:create-categories';
    protected static $defaultDescription = 'Création des 3 catégories';
    private EntityManagerInterface $em;
    private CategoryRepository $repository;

    public function __construct(EntityManagerInterface $em, CategoryRepository $repository, string $name = null)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->repository = $repository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (0 === count($this->repository->findAll())) {
            $cat1 = (new Category())
                ->setName('Bloquant')
                ->setColor('danger');
            $cat2 = (new Category())
                ->setName('Non-Bloquant')
                ->setColor('warning');
            $cat3 = (new Category())
                ->setName('Minime')
                ->setColor('info');
            $this->em->persist($cat1);
            $this->em->persist($cat2);
            $this->em->persist($cat3);
            $this->em->flush();
            $io->success('Catégories créées.');

            return Command::SUCCESS;
        } else {
            $io->error('Les catégories existent déja');

            return Command::FAILURE;
        }
    }
}

