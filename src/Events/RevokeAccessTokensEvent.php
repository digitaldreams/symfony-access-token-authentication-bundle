<?php

namespace AccessToken\Events;

use Symfony\Contracts\EventDispatcher\Event;

class RevokeAccessTokensEvent extends Event
{
    public const NAME='revoke_access_token';

    public function __construct(public readonly int $userId)
    {
    }
}