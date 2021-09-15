<?php

namespace App\Controller;

use App\Entity\PostImage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

class PostImageController extends AbstractController
{
    /**
     * @Route("/post/image/{id}", name="post_image", options={"expose"=true})
     * @IsGranted("POST_IMAGE_VIEW", postImage)
     */
    public function index(PostImage $postImage): BinaryFileResponse
    {
        /**
         * @var string $path
         */
        $path = $this->getParameter('posts_directory');

        return new BinaryFileResponse($path.'/'.$postImage->getImageName());
    }
}
