<?php

namespace AccessToken\EventListener;

use AccessToken\Events\RevokeAccessTokensEvent;
use AccessToken\Persistence\Repository\AccessTokenRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AccessTokenEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private AccessTokenRepository $accessTokenRepository)
    {
    }

    public function onRevokeAccessToken(RevokeAccessTokensEvent $event): void
    {
        print_r('User id:' . $event->userId . ' all token revoked');
        $this->logger->info('User id:' . $event->userId . ' all token revoked');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RevokeAccessTokensEvent::NAME => 'onRevokeAccessToken'
        ];
    }
}