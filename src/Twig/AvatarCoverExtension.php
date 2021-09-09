<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use Symfony\Component\Asset\PackageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AvatarCoverExtension extends AbstractExtension
{
    private const DEFAULT_AVATAR = 'default_avatar.png';
    private const DEFAULT_COVER  = 'default_cover.jpg';

    private PackageInterface $avatarsPackage;
    private PackageInterface $imagesPackage;
    private PackageInterface $coversPackage;

    public function __construct(PackageInterface $avatarsPackage, PackageInterface $imagesPackage, PackageInterface $coversPackage)
    {
        $this->avatarsPackage = $avatarsPackage;
        $this->imagesPackage  = $imagesPackage;
        $this->coversPackage  = $coversPackage;
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
        return null !== $user->getAvatar() ? $this->avatarsPackage->getUrl($user->getAvatar()) : $this->imagesPackage->getUrl(self::DEFAULT_AVATAR);
    }

    public function coverAsset(User $user): string
    {
        return null !== $user->getCover() ? $this->coversPackage->getUrl($user->getCover()) : $this->imagesPackage->getUrl(self::DEFAULT_COVER);
    }
}
