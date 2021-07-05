<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     * @IsGranted("ROLE_USER")
     */
    public function home(): Response
    {
        return $this->render('app/index.html.twig');
    }

    /**
     * @Route("/create", name="app_create_user", methods={"GET"})
     */
    public function createUser(UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $user = (new User())
            ->setEmail('marco.malaspina@reconnect.fr')
            ->setFirstName('Marco')
            ->setLastLogin(new \DateTime('now'))
            ->setRole('ROLE_USER')
            ->setLastName('Malaspina');
        $user->setPassword($hasher->hashPassword($user, '280690'));
        $em->persist($user);
        $em->flush();

        return new Response($user->getFirstName());
    }
}
