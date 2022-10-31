<?php

namespace App\Manager;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

class CommentManager
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function create(Comment $comment): void
    {
        $this->em->persist($comment);
        $this->em->flush();
    }
}
