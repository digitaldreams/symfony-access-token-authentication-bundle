<?php

namespace AccessToken\EventListener;

use AccessToken\Events\RevokeAccessTokensEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AccessTokenEventSubscriber implements EventSubscriberInterface
{
    public function onRevokeAccessToken(RevokeAccessTokensEvent $event): void
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RevokeAccessTokensEvent::class => 'onRevokeAccessToken'
        ];
    }
}