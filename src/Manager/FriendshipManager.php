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
use App\Resolver\UserTopicsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class FriendshipManager
{
    private FormFactoryInterface   $formFactory;
    private EntityManagerInterface $entityManager;
    private UserTopicsResolver     $topicsResolver;
    private HubInterface           $hub;
    private Security               $security;
    private SerializerInterface    $serializer;

    public function __construct(
        FormFactoryInterface   $formFactory,
        EntityManagerInterface $entityManager,
        HubInterface           $hub,
        UserTopicsResolver     $topicsResolver,
        Security               $security,
        SerializerInterface    $serializer
    ) {
        $this->formFactory    = $formFactory;
        $this->entityManager  = $entityManager;
        $this->hub            = $hub;
        $this->topicsResolver = $topicsResolver;
        $this->security       = $security;
        $this->serializer     = $serializer;
    }

    /**
     * @param Friendship|null $friendship
     * @param mixed[]         $options
     *
     * @return FormInterface<string|FormInterface>
     */
    public function getForm(?Friendship $friendship, array $options = []): FormInterface
    {
        $formClass = AddFriendFormType::class;
        if (null !== $friendship) {
            if (FriendshipMapping::ACCEPTED === $friendship->getStatus()) {
                $formClass = RemoveFriendFormType::class;
            } elseif (FriendshipMapping::PENDING === $friendship->getStatus()) {
                $formClass = ($friendship->getSender() !== $this->security->getUser()) ? AnswerFriendFormType::class : PendingFriendFormType::class;
            }
        } else {
            $formClass = AddFriendFormType::class;
        }

        return $this->formFactory->create($formClass, $friendship, $options);
    }

    /**
     * @param FormInterface<string|FormInterface> $form
     * @param Friendship|null                     $friendship
     * @param User                                $user
     * @param User                                $currentUser
     */
    public function handleForm(FormInterface $form, ?Friendship $friendship, User $user, User $currentUser): void
    {
        /**
         * @var Form $form
         */
        $clickedButton = $form->getClickedButton();
        if (null === $friendship) {
            if ($clickedButton === $form->get('add')) {
                $this->addNewFriendship($user, $currentUser);
            }
        } else {
            if (FriendshipMapping::PENDING === $friendship->getStatus()) {
                if ($clickedButton === $form->get('remove')) {
                    $this->refuseFriendship($friendship);
                } elseif ($clickedButton === $form->get('add')) {
                    $this->acceptFriendship($friendship);
                }
            } elseif (FriendshipMapping::ACCEPTED === $friendship->getStatus()) {
                if ($clickedButton === $form->get('remove')) {
                    $this->removeFriendship($friendship);
                }
            }
        }
    }

    /**
     * @param User $user
     * @param User $currentUser
     */
    private function addNewFriendship(User $user, User $currentUser): void
    {
        $friendship = new Friendship();
        $friendship->setSender($currentUser);
        $friendship->setReceiver($user);
        $this->entityManager->persist($friendship);
        $this->entityManager->flush();
        $this->publishNewFriendshipUpdate($friendship);
    }

    /**
     * @param Friendship $friendship
     */
    private function refuseFriendship(Friendship $friendship): void
    {
        $friendship->setStatus(FriendshipMapping::REFUSED);
        $this->entityManager->flush();
        $this->publishRefusedFriendshipUpdate($friendship);
    }

    /**
     * @param Friendship $friendship
     */
    private function acceptFriendship(Friendship $friendship): void
    {
        $friendship->setStatus(FriendshipMapping::ACCEPTED);
        $this->entityManager->flush();
        $this->publishAcceptedFriendshipUpdate($friendship);
    }

    /**
     * @param Friendship $friendship
     */
    private function removeFriendship(Friendship $friendship): void
    {
        $friendship->setStatus(FriendshipMapping::REMOVED);
        $this->entityManager->flush();
        $this->publishRemovedFriendshipUpdate($friendship);
    }

    /**
     * @param Friendship $friendship
     */
    private function publishNewFriendshipUpdate(Friendship $friendship): void
    {
        $this->publishUpdates($this->buildUpdates($friendship, ['status' => 'newFriendship']));
    }

    /**
     * @param Friendship $friendship
     */
    private function publishRefusedFriendshipUpdate(Friendship $friendship): void
    {
        $this->publishUpdates($this->buildUpdates($friendship, ['status' => 'refusedFriendship']));
    }

    /**
     * @param Friendship $friendship
     */
    private function publishAcceptedFriendshipUpdate(Friendship $friendship): void
    {
        $this->publishUpdates($this->buildUpdates($friendship, ['status' => 'acceptedFriendship']));
    }

    /**
     * @param Friendship $friendship
     */
    private function publishRemovedFriendshipUpdate(Friendship $friendship): void
    {
        $this->publishUpdates($this->buildUpdates($friendship, ['status' => 'removedFriendship']));
    }

    /**
     * @param Friendship $friendship
     * @param mixed[]    $data
     *
     * @return Update[]
     */
    private function buildUpdates(Friendship $friendship, array $data): array
    {
        $updates            = [];
        $friendshipData     = $this->serializer->serialize($friendship, 'json', ['groups' => 'friendship']);
        $data['friendship'] = $friendshipData;

        /**
         * @var User $sender
         */
        $sender    = $friendship->getSender();
        $updates[] = $this->buildSingleUpdate($sender, $data);
        /**
         * @var User $receiver
         */
        $receiver  = $friendship->getReceiver();
        $updates[] = $this->buildSingleUpdate($receiver, $data);

        return $updates;
    }

    /**
     * @param User    $user
     * @param mixed[] $data
     *
     * @return Update
     */
    private function buildSingleUpdate(User $user, array $data): Update
    {
        $topic         = $this->topicsResolver->getFriendshipTopic($user);
        $data['topic'] = $topic;

        return new Update($topic, (string)json_encode($data), true);
    }

    /**
     * @param Update[] $buildUpdates
     */
    private function publishUpdates(array $buildUpdates): void
    {
        foreach ($buildUpdates as $update) {
            $this->hub->publish($update);
        }
    }
}
