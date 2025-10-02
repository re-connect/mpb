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
        $feature = new Bug();

        $vote1 = new Vote();
        $vote2 = new Vote();
        $feature->addVote($vote1);
        $feature->addVote($vote2);

        $comment1 = new Comment();
        $comment2 = new Comment();
        $feature->addComment($comment1);
        $feature->addComment($comment2);

        $this->assertEquals(2, $feature->getLikes());
    }

    public function testGetLikesWithOnlyComments(): void
    {
        $feature = new Bug();

        $comment1 = new Comment();
        $comment2 = new Comment();
        $feature->addComment($comment1);
        $feature->addComment($comment2);

        $this->assertEquals(0, $feature->getLikes());
    }

    public function testGetLikesWithOnlyVotes(): void
    {
        $feature = new Bug();

        $vote1 = new Vote();
        $feature->addVote($vote1);

        $this->assertEquals(1, $feature->getLikes());
    }

    public function testGetLikesWithoutVotesAndComments(): void
    {
        $feature = new Bug();

        $this->assertEquals(0, $feature->getLikes());
    }
}
