<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostImage;
use App\Entity\User;
use App\Form\PostType;
use App\Manager\ImagesManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Route("/add", name="post_add", options={"expose"=true})
     */
    public function index(Request $request, ImagesManager $imagesManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($form->get('postImages')->getData() as $image) {
                if ($image) {
                    $imageName = $imagesManager->uploadPostImage($image);
                    if (null !== $imageName) {
                        $postImage = new PostImage($imageName);
                        $post->addPostImage($postImage);
                        $entityManager->persist($postImage);
                    }
                }
            }
            /**
             * @var User $user
             */
            $user = $this->getUser();
            $post->setCreatedBy($user);
            $entityManager->persist($post);
            $entityManager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/add/front", name="post_add_front", options={"expose"=true})
     */
    public function postAddForm(Request $request): JsonResponse
    {
        $form = $this->createForm(PostType::class);

        return new JsonResponse($this->renderView('form/post_form.html.twig', [
            'form' => $form->createView()
        ]));
    }
}
