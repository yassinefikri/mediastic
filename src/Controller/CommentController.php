<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Event\CommentEditedEvent;
use App\Event\CommentPostedEvent;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Route("/front", name="comment_form_front", options={"expose"=true})
     * @Route("/edit/{id}/front", name="edit_comment_front", options={"expose"=true}, requirements={"id"="^[1-9]\d*$"})
     */
    public function getForm(string $_route, Comment $comment = null): Response
    {
        if ('edit_comment_front' === $_route) {
            if (null === $comment) {
                throw new NotFoundHttpException();
            } else {
                $this->denyAccessUnlessGranted('COMMENT_EDIT', $comment);
            }
        }
        $form = $this->createForm(CommentType::class, $comment);

        return new JsonResponse($this->renderView('form/comment_form.html.twig', [
            'form' => $form->createView()
        ]));
    }

    /**
     * @Route("/add/{id}", name="add_comment", options={"expose"=true}, methods={"POST"}, requirements={"id"="^[1-9]\d*$"})
     */
    public function addComment(Request $request, Post $post, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $comment = new Comment();
        $form    = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            /**
             * @var User $user
             */
            $user = $this->getUser();
            $comment->setOwner($user);
            $post->addComment($comment);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            $eventDispatcher->dispatch(new CommentPostedEvent($comment));

            return $this->json($comment, Response::HTTP_OK, [], ['groups' => ['json', 'comment']]);
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/{id}", name="get_post_comments", options={"expose"=true}, requirements={"id"="^[1-9]\d*$"})
     */
    public function getPostComments(PostRepository $postRepository, int $id): JsonResponse
    {
        $post = $postRepository->getPostWithComments($id);
        if (null === $post) {
            throw new NotFoundHttpException();
        }
        /**
         * @var Post $post
         */
        $this->denyAccessUnlessGranted('POST_VIEW', $post);

        return $this->json($post->getComments(), Response::HTTP_OK, [], ['groups' => 'comment']);
    }

    /**
     * @Route("/edit/{id}", name="edit_comment", options={"expose"=true}, methods={"POST"}, requirements={"id"="^[1-9]\d*$"})
     * @IsGranted("COMMENT_EDIT", subject="comment")
     */
    public function editComment(Request $request, Comment $comment, EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $eventDispatcher->dispatch(new CommentEditedEvent($comment));

            return $this->json($comment, Response::HTTP_OK, [], ['groups' => ['json', 'comment']]);
        }

        return new JsonResponse($this->formGetErrors($form), Response::HTTP_BAD_REQUEST);
    }
}
