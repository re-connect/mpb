<?php

namespace App\Tests\Mapper;

use App\Entity\Application;
use App\Entity\Attachment;
use App\Entity\Bug;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Vote;
use App\Mapper\UserRequestMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRequestMapperTest extends KernelTestCase
{
    public function testMap(): void
    {
        $bug = new Bug();
        $bug
            ->setUser((new User())->setFirstName('testUser'))
            ->setTitle('testTitle')
            ->setContent('testContent')
            ->setCreatedAt(\DateTimeImmutable::createFromMutable(new \DateTime()))
            ->setDraft(true)
            ->setApplication(new Application())
            ->addAttachment((new Attachment())->setName('testAttachment'))
            ->addComment((new Comment())->setContent('testComment'))
            ->addVote(new Vote());

        $feature = UserRequestMapper::mapBugToFeature($bug);

        self::assertEquals($bug->getUser(), $feature->getUser());
        self::assertEquals($bug->getTitle(), $feature->getTitle());
        self::assertEquals($bug->getContent(), $feature->getContent());
        self::assertEquals($bug->getCreatedAt(), $feature->getCreatedAt());
        self::assertFalse($feature->isDraft());
        self::assertEquals($bug->getApplication(), $feature->getApplication());
        self::assertEquals($bug->getAttachments(), $feature->getAttachments());
        self::assertEquals($bug->getComments(), $feature->getComments());
        self::assertEquals($bug->getVotes(), $feature->getVotes());
    }
}
