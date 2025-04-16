<?php

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Issue;
use App\Repository\AttachmentRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class AttachmentService
{
    public function __construct(
        private readonly AttachmentRepository $attachmentRepo,
        private readonly ParameterBagInterface $parameters,
    ) {
    }

    public function add(Attachment $attachment): void
    {
        $this->attachmentRepo->add($attachment, true);
    }

    public function generateNewFilename(UploadedFile $file): string
    {
        return uniqid(more_entropy: true) . '.' . $file->guessExtension();
    }

    public function handleUploadedFile(Issue $issue, Request $request): ?Attachment
    {
        /** @var ?UploadedFile $attachmentFile */
        $attachmentFile = $request->files->get('attachment');

        if (null === $attachmentFile) {
            return null;
        }

        $newFilename = $this->generateNewFilename($attachmentFile);

        $attachment = new Attachment($issue);
        $attachment->setOriginalName($attachmentFile->getClientOriginalName());
        $attachment->setPath($this->parameters->get('absolute_attachments_directory') . DIRECTORY_SEPARATOR . $newFilename);
        $attachment->setSize($attachmentFile->getSize());

        $attachmentFile->move($this->parameters->get('attachments_directory'), $newFilename);

        $this->add($attachment);

        return $attachment;
    }
}
