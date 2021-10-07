<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\PostImage;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

class PostImageDeleteListener
{
    private Filesystem $filesystem;

    private string $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->filesystem      = new Filesystem();
        $this->targetDirectory = $targetDirectory;
    }

    public function postRemove(PostImage $postImage): void
    {
        $imageName = $postImage->getImageName();
        if (null !== $imageName) {
            $path = $this->targetDirectory.'/'.$imageName;
            if (true === $this->filesystem->exists($path)) {
                $this->filesystem->remove($path);
            }
        }
    }
}