<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Entity\PostImage;
use Symfony\Component\Routing\RouterInterface;

class PostImageResolver
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getPublicUrl(PostImage $postImage): string
    {
        return $this->router->generate('post_image', ['id' => $postImage->getId()]);
    }
}
