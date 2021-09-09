<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountGeneralType;
use App\Form\AccountPasswordType;
use App\Form\AvatarCoverType;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/general", name="user_account_general")
     */
    public function general(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountGeneralType::class, $user);
        $form->handleRequest($request);
        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                'Your account has been updated'
            );
        }

        return $this->render('user/index.html.twig', [
            'form'        => $form->createView(),
            'currentPill' => 'general'
        ]);
    }

    /**
     * @Route("/password", name="user_account_password")
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
            $this->addFlash(
                'success',
                'Your account has been updated'
            );
        }

        return $this->render('user/index.html.twig', [
            'form'        => $form->createView(),
            'currentPill' => 'password'
        ]);
    }

    /**
     * @Route("/avatar", name="user_account_avatar")
     */
    public function avatar(Request $request, ImageUploader $imageUploader): Response
    {
        $form = $this->createForm(AvatarCoverType::class);
        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $images = ['avatar' => 'setAvatar', 'cover' => 'setCover'];
            $user = $this->getUser();
            foreach($images as $image => $setter){
                $imageFile = $form->get($image)->getData();
                if ($imageFile) {
                    $imageFileName = $imageUploader->upload($imageFile, $image);
                    if(null !== $imageFileName) {
                        $user->$setter($imageFileName);
                    } else {
                        $this->addFlash(
                            'danger',
                            "There was an error processing your {$image}"
                        );
                    }
                }
            }

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('user/avatar.html.twig', [
            'form'        => $form->createView(),
            'currentPill' => 'avatar'
        ]);
    }
}
