<?php

namespace App\Mapping;

class FriendshipMapping
{
    public const ACCEPTED = 'accepted';
    public const PENDING  = 'pending';
    public const REFUSED  = 'refused';
    public const REMOVED  = 'removed';

    public const ICON_ADD      = '<i class="bi bi-person-plus-fill"></i> Add';
    public const ICON_ACCEPT   = '<i class="bi bi-person-check-fill"></i> Accept';
    public const ICON_REFUSE   = '<i class="bi bi-person-dash-fill"></i>';
    public const ICON_REMOVE   = '<i class="bi bi-person-dash-fill"></i> Remove';
    public const ICON_ACCEPTED = '<i class="bi bi-person-check-fill"></i>';
    public const ICON_PENDING  = '<i class="bi bi-person-dash-fill"></i> Pending';
}
