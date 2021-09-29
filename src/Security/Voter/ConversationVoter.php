<?php

namespace App\Security\Voter;

use App\Entity\Conversation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ConversationVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['SEND_MESSAGE', 'READ_MESSAGES'])
            && $subject instanceof Conversation;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'SEND_MESSAGE':
            case 'READ_MESSAGES':
                return $subject->getParticipants()->contains($user);
        }

        return false;
    }
}
