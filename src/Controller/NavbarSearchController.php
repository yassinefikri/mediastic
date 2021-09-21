<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NavbarSearchType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/navbar/search")
 */
class NavbarSearchController extends AbstractController
{
    /**
     * @Route("/", name="navbar_search", options={"expose"=true})
     */
    public function index(Request $request): JsonResponse
    {
        $form = $this->createForm(NavbarSearchType::class);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            /**
             * @var UserRepository $userRepository
             */
            $userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);

            return $this->json($userRepository->searchUsers($form->get('query')->getData()), Response::HTTP_OK, [], ['groups' => 'json']);
        }

        return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/front", name="navbar_search_front", options={"expose"=true})
     */
    public function front(): JsonResponse
    {
        $form = $this->createForm(NavbarSearchType::class);

        return $this->json($this->renderView('form/navbar_search.html.twig', [
            'form' => $form->createView()
        ]));
    }
}
