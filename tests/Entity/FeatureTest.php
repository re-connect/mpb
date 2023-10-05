<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Feature;
use App\Entity\Vote;
use App\Repository\FeatureRepository;
use App\Tests\Factory\FeatureFactory;
use App\Tests\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FeatureTest extends KernelTestCase
{
    protected ValidatorInterface $validator;
    protected EntityManagerInterface $em;
    protected FeatureRepository $featureRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->featureRepository = self::getContainer()->get(FeatureRepository::class);
    }

    public function testDeleteFeature(): void
    {
        /** @var Feature $feature */
        $feature = FeatureFactory::randomOrCreate()->object();
        $id = $feature->getId();
        $comment = (new Comment())->setContent('comment')->setUser(UserFactory::randomOrCreate()->object())->setFeature($feature);
        $vote = (new Vote())->setFeature($feature)->setVoter(UserFactory::randomOrCreate()->object());
        $this->em->persist($comment);
        $this->em->persist($vote);
        $this->em->flush();

        $this->em->remove($feature);
        $this->em->flush();

        // Assert feature having vote and comment has been deleted
        $this->assertEmpty(FeatureFactory::findBy(['id' => $id]));
    }
}
