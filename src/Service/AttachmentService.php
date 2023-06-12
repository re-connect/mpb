<?php

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\UserRequest;
use App\Traits\UserAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AttachmentService
{
    use UserAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly string $uploadsDirectory,
        private readonly ValidatorInterface $validator
    ) {
    }

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

        if ($this->validator->validate($attachment)->count() > 0) {
            return false;
        }

        $this->em->persist($attachment);
        $userRequest->addAttachment($attachment);
        $this->em->flush();

        $file->move($this->uploadsDirectory, $name);

        return true;
    }
}
