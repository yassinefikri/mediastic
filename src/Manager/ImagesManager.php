<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\PostImage;
use App\Entity\User;
use LogicException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImagesManager
{
    /**
     * @var string[] $targetDirectories
     */
    private array            $targetDirectories;
    private SluggerInterface $slugger;
    private Filesystem       $filesystem;

    /**
     * @param string[] $targetDirectories
     */
    public function __construct(array $targetDirectories, SluggerInterface $slugger)
    {
        $this->slugger           = $slugger;
        $this->targetDirectories = $targetDirectories;
        $this->filesystem        = new Filesystem();
    }

    public function uploadAvatar(UploadedFile $file, User $user): ?string
    {
        $imageName = $this->upload($file, 'avatar');
        if (null !== $imageName) {
            $this->removeAvatar($user);
            $user->setAvatar($imageName);
        }

        return $imageName;
    }

    public function uploadCover(UploadedFile $file, User $user): ?string
    {
        $imageName = $this->upload($file, 'cover');
        if (null !== $imageName) {
            $this->removeCover($user);
            $user->setCover($imageName);
        }

        return $imageName;
    }

    public function uploadPostImage(UploadedFile $file): ?string
    {
        return $this->upload($file, 'post');
    }

    public function removeAvatar(User $user): void
    {
        if (null !== $user->getAvatar()) {
            $path = $this->targetDirectories['avatar'].'/'.$user->getAvatar();
            $this->removeImage($path);
            $user->setAvatar(null);
        }
    }

    public function removeCover(User $user): void
    {
        if (null !== $user->getCover()) {
            $path = $this->targetDirectories['cover'].'/'.$user->getCover();
            $this->removeImage($path);
            $user->setCover(null);
        }
    }

    private function removeImage(string $path): void
    {
        if (true === $this->filesystem->exists($path)) {
            $this->filesystem->remove($path);
        }
    }

    private function upload(UploadedFile $file, string $type): ?string
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

    private function getTargetDirectory(string $type): string
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
