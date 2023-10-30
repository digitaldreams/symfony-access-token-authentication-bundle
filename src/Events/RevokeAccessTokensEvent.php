<?php

namespace AccessToken\Events;

use Symfony\Contracts\EventDispatcher\Event;

class RevokeAccessTokensEvent extends Event
{

    public function __construct(public readonly int $userId)
    {
    }
}