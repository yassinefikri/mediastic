<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use App\Resolver\UserInfosResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AvatarCoverExtension extends AbstractExtension
{
    private UserInfosResolver $infosResolver;

    public function __construct(UserInfosResolver $infosResolver)
    {
        $this->infosResolver = $infosResolver;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('avatar', [$this, 'avatarAsset']),
            new TwigFilter('cover', [$this, 'coverAsset']),
        ];
    }

    public function avatarAsset(User $user): string
    {
        return $this->infosResolver->getAvatarAsset($user);
    }

    public function coverAsset(User $user): string
    {
        return $this->infosResolver->getCoverAsset($user);
    }
}
