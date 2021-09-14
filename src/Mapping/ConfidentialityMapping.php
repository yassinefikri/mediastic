<?php

namespace App\Mapping;

class ConfidentialityMapping
{
    public const STATUS_PRIVATE = 'private';
    public const STATUS_FRIENDS = 'friends';
    public const STATUS_PUBLIC  = 'public';

    public const confs = [
        'Public' => self::STATUS_PUBLIC,
        'Friends only' => self::STATUS_FRIENDS,
        'Private' => self::STATUS_PRIVATE,
    ];
}