<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Conversation;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class FriendshipNormalizer implements ContextAwareNormalizerInterface
{
    public const FORMAT = 'json';

    private NormalizerInterface $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
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
        return $data instanceof Conversation && self::FORMAT === $format;
    }

    /**
     * @param mixed       $object
     * @param string|null $format
     * @param mixed[]     $context
     *
     * @return mixed[]
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        /**
         * @var mixed[] $normalizedConversation
         */
        $normalizedConversation = $this->normalizer->normalize($object, $format, $context);
        if (true === array_key_exists('participants', $normalizedConversation)) {
            $participants = [];
            foreach ($normalizedConversation['participants'] as $participant) {
                $participants[$participant['username']] = $participant;
            }
            $normalizedConversation['participants'] = $participants;
        }

        return $normalizedConversation;
    }
}