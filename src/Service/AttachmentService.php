<?php

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\UserRequest;
use App\Traits\UserAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class AttachmentService
{
    use UserAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly string $uploadsDirectory,
    ) {
    }

    /**
     * @param mixed $file
     */
    public function addAttachment(UserRequest $userRequest, mixed $file): bool
    {
        if (!$file instanceof UploadedFile || !$fileExtension = $file->guessExtension()) {
            return false;
        }

        $name = sprintf('%s.%s',
            Uuid::v4(),
            $fileExtension
        );
        $attachment = (new Attachment())
            ->setUserRequest($userRequest)
            ->setName($name)
            ->setSize($file->getSize())
            ->setUploadedBy($this->getUser());
        $this->em->persist($attachment);
        $this->em->flush();
        $userRequest->addAttachment($attachment);

        $file->move($this->uploadsDirectory, $name);

        return true;
    }
}
