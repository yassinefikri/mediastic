<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\PostImage;
use App\Mapping\ConfidentialityMapping;
use App\Repository\FriendshipRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostImageVoter extends Voter
{
    private FriendshipRepository $friendshipRepository;

    public function __construct(FriendshipRepository $friendshipRepository)
    {
        $this->friendshipRepository = $friendshipRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['POST_IMAGE_EDIT', 'POST_IMAGE_VIEW'])
            && $subject instanceof PostImage;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'POST_IMAGE_EDIT':
                return $subject->getPost()->getCreatedBy() === $user;
            case 'POST_IMAGE_VIEW':
                return $subject->getPost()->getCreatedBy() === $user
                    || ConfidentialityMapping::STATUS_PUBLIC === $subject->getPost()->getConfidentiality()
                    || (ConfidentialityMapping::STATUS_FRIENDS === $subject->getPost()->getConfidentiality()
                        && true === $this->friendshipRepository->isFriends($user, $subject->getPost()->getCreatedBy())
                    );
        }

        return false;
    }
}
