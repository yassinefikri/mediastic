<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\PostImage;
use App\Resolver\PostImageResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PostImageExtension extends AbstractExtension
{
    private PostImageResolver $postImageResolver;

    public function __construct(PostImageResolver $postImageResolver)
    {
        $this->postImageResolver = $postImageResolver;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('publicUrl', [$this, 'getPublicUrl']),
        ];
    }

    public function getPublicUrl(PostImage $postImage): string
    {
        return $this->postImageResolver->getPublicUrl($postImage);
    }
}
