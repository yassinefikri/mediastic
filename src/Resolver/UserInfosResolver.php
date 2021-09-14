<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Entity\User;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserInfosResolver
{
    private const DEFAULT_AVATAR = 'default_avatar.png';
    private const DEFAULT_COVER  = 'default_cover.jpg';

    private PackageInterface    $avatarsPackage;
    private PackageInterface    $imagesPackage;
    private PackageInterface    $coversPackage;
    private NormalizerInterface $normalizer;

    public function __construct(PackageInterface $imagesPackage, PackageInterface $avatarsPackage, PackageInterface $coversPackage, NormalizerInterface $normalizer)
    {
        $this->avatarsPackage = $avatarsPackage;
        $this->imagesPackage  = $imagesPackage;
        $this->coversPackage  = $coversPackage;
        $this->normalizer     = $normalizer;
    }

    public function getAvatarAsset(User $user): string
    {
        return null !== $user->getAvatar() ? $this->avatarsPackage->getUrl($user->getAvatar()) : $this->imagesPackage->getUrl(self::DEFAULT_AVATAR);
    }

    public function getCoverAsset(User $user): string
    {
        return null !== $user->getCover() ? $this->coversPackage->getUrl($user->getCover()) : $this->imagesPackage->getUrl(self::DEFAULT_COVER);
    }

    /**
     * @param User $user
     *
     * @return array<string,string>
     */
    public function getUserInfos(User $user): array
    {
        try {
            /**
             * @var array<string,string> $userInfos
             */
            $userInfos = $this->normalizer->normalize($user, 'json', ['groups' => 'whoami']);
        } catch (ExceptionInterface $e) {

            return [];
        }
        $userInfos['avatar_url'] = $this->getAvatarAsset($user);
        $userInfos['cover_url']  = $this->getCoverAsset($user);

        return $userInfos;
    }
}
