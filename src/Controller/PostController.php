<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Manager\ImagesManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Route("/add", name="post_add", options={"expose"=true})
     * @Route("/edit/{id}", name="post_edit", options={"expose"=true}, requirements={"id"="^[1-9]\d*$"})
     */
    public function index(Request $request, ImagesManager $imagesManager, string $_route, Post $post = null): Response
    {
        if ('post_edit' === $_route) {
            if (null === $post) {
                throw new NotFoundHttpException();
            } else {
                $this->denyAccessUnlessGranted('POST_EDIT', $post);
            }
        } else {
            $post = new Post();
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            if (null === $post->getId()) {
                /**
                 * @var User $user
                 */
                $user = $this->getUser();
                $post->setCreatedBy($user);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return new JsonResponse();
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/add/front", name="post_add_front", options={"expose"=true})
     * @Route("/edit/{id}/front", name="post_edit_front", options={"expose"=true}, requirements={"id"="^[1-9]\d*$"})
     */
    public function postAddForm(string $_route, Post $post = null): JsonResponse
    {
        if ('post_edit_front' === $_route) {
            if (null === $post) {
                throw new NotFoundHttpException();
            } else {
                $this->denyAccessUnlessGranted('POST_EDIT', $post);
            }
        }

        $form = $this->createForm(PostType::class, $post);

        return new JsonResponse($this->renderView('form/post_form.html.twig', [
            'form' => $form->createView()
        ]));
    }

    /**
     * @Route("/view/{id}", name="post_view", options={"expose"=true}, requirements={"id"="^[1-9]\d*$"})
     * @IsGranted("POST_VIEW", subject="post")
     */
    public function viewPost(Post $post): JsonResponse
    {
        return $this->json($post, Response::HTTP_OK, [], ['groups' => 'json']);
    }
}
