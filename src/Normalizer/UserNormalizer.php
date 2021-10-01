<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\User;
use App\Resolver\UserAssetsResolver;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserNormalizer implements ContextAwareNormalizerInterface
{
    public const FORMAT = 'json';

    private NormalizerInterface $normalizer;
    private UserAssetsResolver  $assetsResolver;

    public function __construct(ObjectNormalizer $normalizer, UserAssetsResolver $assetsResolver)
    {
        $this->normalizer     = $normalizer;
        $this->assetsResolver = $assetsResolver;
    }

    /**
     * @param mixed       $data
     * @param string|null $format
     * @param mixed[]     $context
     *
     * @return bool
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof User && self::FORMAT === $format;
    }

    /**
     * @param mixed       $object
     * @param string|null $format
     * @param mixed[]     $context
     *
     * @return string[]
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        /**
         * @var string[] $normalizedUser
         */
        $normalizedUser               = $this->normalizer->normalize($object, $format, $context);
        $normalizedUser['avatar_url'] = $this->assetsResolver->getAvatarAsset($object);
        $normalizedUser['cover_url']  = $this->assetsResolver->getCoverAsset($object);

        return $normalizedUser;
    }
}
