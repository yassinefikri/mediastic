<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountGeneralType;
use App\Form\AccountPasswordType;
use App\Form\AvatarCoverType;
use App\Resolver\UserInfosResolver;
use App\Service\FormProvider;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/general", name="user_account_general", options={"expose"=true})
     */
    public function general(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountGeneralType::class, $user);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->forward('App\Controller\UserController::whoami');
        }

        return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/password", name="user_account_password", options={"expose"=true})
     */
    public function password(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $form = $this->createForm(AccountPasswordType::class, $user);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();

            return $this->forward('App\Controller\UserController::whoami');
        }

        return new JsonResponse(null ,Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/avatar", name="user_account_avatar", options={"expose"=true})
     */
    public function avatar(Request $request, ImageUploader $imageUploader): Response
    {
        $form = $this->createForm(AvatarCoverType::class);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $images = ['avatar' => 'setAvatar', 'cover' => 'setCover'];
            $user   = $this->getUser();
            foreach ($images as $image => $setter) {
                $imageFile = $form->get($image)->getData();
                if ($imageFile) {
                    $imageFileName = $imageUploader->upload($imageFile, $image);
                    if (null !== $imageFileName) {
                        $user->$setter($imageFileName);
                    }
                }
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->forward('App\Controller\UserController::whoami');
        }

        return new JsonResponse(null ,Response::HTTP_BAD_REQUEST);
    }

    /**
     * @route("/infos", name="user_infos", options={"expose"=true})
     */
    public function whoami(UserInfosResolver $resolver): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        return $this->json($resolver->getUserInfos($user));
    }

    /**
     * @Route("/general/front", name="user_account_general_front", options={"expose"=true})
     * @Route("/password/front", name="user_account_password_front", options={"expose"=true})
     * @Route("/avatar/front", name="user_account_avatar_front", options={"expose"=true})
     */
    public function getUserForm(string $_route, FormProvider $formProvider): JsonResponse
    {
        return $formProvider->getResponse($_route, $this->getUser());
    }
}
