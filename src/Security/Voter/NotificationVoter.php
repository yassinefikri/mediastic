<?php

namespace App\Security\Voter;

use App\Entity\AbstractNotification;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['NOTIFICATION_UPDATE_SEEN', 'NOTIFICATION_REMOVE'])
            && $subject instanceof AbstractNotification;
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
            case 'NOTIFICATION_REMOVE':
                return $subject->getUser() === $user;
            case 'NOTIFICATION_UPDATE_SEEN':
                return $subject->getUser() === $user && false === $subject->getSeen();
        }

        return false;
    }
}
