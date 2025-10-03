<?php

namespace App\Tests\Entity;

use App\Entity\Bug;
use App\Entity\Comment;
use App\Entity\Vote;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BugTest extends KernelTestCase
{
    public function testGetLikesWithVotesAndComments(): void
    {
        $bug = new Bug();

        $bug->addVote(new Vote());
        $bug->addVote(new Vote());

        $bug->addComment(new Comment());
        $bug->addComment(new Comment());

        $this->assertEquals(2, $bug->getLikes());
    }

    public function testGetLikesWithOnlyComments(): void
    {
        $bug = new Bug();

        $bug->addComment(new Comment());
        $bug->addComment(new Comment());

        $this->assertEquals(0, $bug->getLikes());
    }

    public function testGetLikesWithOnlyVotes(): void
    {
        $bug = new Bug();

        $bug->addVote(new Vote());

        $this->assertEquals(1, $bug->getLikes());
    }

    public function testGetLikesWithoutVotesAndComments(): void
    {
        $bug = new Bug();

        $this->assertEquals(0, $bug->getLikes());
    }
}
