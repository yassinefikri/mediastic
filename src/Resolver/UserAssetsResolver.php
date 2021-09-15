<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Entity\User;
use Symfony\Component\Asset\PackageInterface;

class UserAssetsResolver
{
    private const DEFAULT_AVATAR = 'default_avatar.png';
    private const DEFAULT_COVER  = 'default_cover.jpeg';

    private PackageInterface $avatarsPackage;
    private PackageInterface $imagesPackage;
    private PackageInterface $coversPackage;

    public function __construct(PackageInterface $imagesPackage, PackageInterface $avatarsPackage, PackageInterface $coversPackage)
    {
        $this->avatarsPackage = $avatarsPackage;
        $this->imagesPackage  = $imagesPackage;
        $this->coversPackage  = $coversPackage;
    }

    public function getAvatarAsset(User $user): string
    {
        return null !== $user->getAvatar() ? $this->avatarsPackage->getUrl($user->getAvatar()) : $this->imagesPackage->getUrl(self::DEFAULT_AVATAR);
    }

    public function getCoverAsset(User $user): string
    {
        return null !== $user->getCover() ? $this->coversPackage->getUrl($user->getCover()) : $this->imagesPackage->getUrl(self::DEFAULT_COVER);
    }
}
