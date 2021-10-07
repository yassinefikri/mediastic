<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use App\Mapping\ConfidentialityMapping;
use App\Repository\FriendshipRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    private FriendshipRepository $friendshipRepository;

    public function __construct(FriendshipRepository $friendshipRepository)
    {
        $this->friendshipRepository = $friendshipRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return true === in_array($attribute, ['POST_EDIT', 'POST_VIEW'])
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /**
         * @var User $user
         */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'POST_EDIT':
                return $subject->getCreatedBy() === $user;
            case 'POST_VIEW':
                return $subject->getCreatedBy() === $user
                    || ConfidentialityMapping::STATUS_PUBLIC === $subject->getConfidentiality()
                    || (ConfidentialityMapping::STATUS_FRIENDS === $subject->getConfidentiality()
                        && true === $this->friendshipRepository->isFriends($user, $subject->getCreatedBy())
                    );
        }

        return false;
    }
}
