<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Friendship;
use App\Entity\User;
use App\Form\AddFriendFormType;
use App\Form\AnswerFriendFormType;
use App\Form\PendingFriendFormType;
use App\Form\RemoveFriendFormType;
use App\Mapping\FriendshipMapping;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FriendshipManager
{
    private FormFactoryInterface   $formFactory;
    private EntityManagerInterface $entityManager;

    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager)
    {
        $this->formFactory   = $formFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Friendship|null      $friendship
     * @param array<string,string> $options
     *
     * @return FormInterface
     */
    public function getForm(?Friendship $friendship, User $user, User $currentUser, array $options = []): FormInterface
    {
        $formClass = AddFriendFormType::class;
        if (null !== $friendship) {
            switch ($friendship->getStatus()) {
                case FriendshipMapping::ACCEPTED:
                    $formClass = RemoveFriendFormType::class;
                    break;
                case FriendshipMapping::PENDING:
                    $formClass = ($friendship->getSender() !== $currentUser) ? AnswerFriendFormType::class : PendingFriendFormType::class;
                    break;
            }
        } else {
            $formClass = AddFriendFormType::class;
        }

        return $this->formFactory->create($formClass, $friendship, $options);
    }

    /**
     * @param FormInterface   $form
     * @param Friendship|null $friendship
     * @param User            $user
     * @param User            $currentUser
     */
    public function handleForm(FormInterface $form, ?Friendship $friendship, User $user, User $currentUser)
    {
        $clickedButton = $form->getClickedButton();
        if (null === $friendship) {
            if ($clickedButton === $form->get('add')) {
                $friendship = new Friendship();
                $friendship->setSender($currentUser);
                $friendship->setReceiver($user);
            }
        } else {
            switch ($friendship->getStatus()) {
                case FriendshipMapping::PENDING:
                    if ($clickedButton === $form->get('remove')) {
                        $friendship->setStatus(FriendshipMapping::REFUSED);
                    } elseif ($clickedButton === $form->get('add')) {
                        $friendship->setStatus(FriendshipMapping::ACCEPTED);
                    }
                    break;
                case FriendshipMapping::ACCEPTED:
                    if ($clickedButton === $form->get('remove')) {
                        $friendship->setStatus(FriendshipMapping::REMOVED);
                    }
                    break;
            }
        }
        $this->entityManager->persist($friendship);
        $this->entityManager->flush();
    }
}
