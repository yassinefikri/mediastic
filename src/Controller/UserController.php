<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountGeneralType;
use App\Form\AccountPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 *  @Route("/account")
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
        if(true === $form->isSubmitted() && true === $form->isValid()){
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                'Your account has been updated'
            );
        }

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
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
        if(true === $form->isSubmitted() && true === $form->isValid()){
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
            'form' => $form->createView(),
            'currentPill' => 'password'
        ]);
    }
}
