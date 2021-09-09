<?php

declare(strict_types=1);

namespace App\Service;

use LogicException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use function uniqid;
use function array_key_exists;
use function pathinfo;

class ImageUploader
{
    /**
     * @var string[]
     */
    private array            $targetDirectories;
    private SluggerInterface $slugger;

    /**
     * @param string[]         $targetDirectories
     * @param SluggerInterface $slugger
     */
    public function __construct(array $targetDirectories, SluggerInterface $slugger)
    {
        $this->targetDirectories = $targetDirectories;
        $this->slugger           = $slugger;
    }

    public function upload(UploadedFile $file, string $type): ?string
    {
        $this->supports($type);
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename     = $this->slugger->slug($originalFilename);
        $fileName         = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory($type), $fileName);
        } catch (FileException $e) {
            return null;
        }

        return $fileName;
    }

    public function getTargetDirectory(string $type): string
    {
        return $this->targetDirectories[$type];
    }

    private function supports(string $type): void
    {
        if (false === array_key_exists($type, $this->targetDirectories)) {
            throw new LogicException("Image Uploader doesnt support type : {$type}");
        }
    }
}
