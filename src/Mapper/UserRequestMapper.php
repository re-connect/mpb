<?php

namespace App\Mapper;

use App\Entity\Bug;
use App\Entity\Feature;

class UserRequestMapper
{
    public static function mapBugToFeature(Bug $bug): Feature
    {
        $feature = (new Feature())->setUser($bug->getUser())
            ->setTitle($bug->getTitle())
            ->setContent($bug->getContent())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($bug->getCreatedAt()))
            ->setDraft(false)
            ->setApplication($bug->getApplication());

        foreach ($bug->getAttachments() as $attachment) {
            $feature->addAttachment($attachment);
        }
        foreach ($bug->getComments() as $comment) {
            $feature->addComment($comment);
        }
        foreach ($bug->getVotes() as $vote) {
            $feature->addVote($vote);
        }

        return $feature;
    }
}
