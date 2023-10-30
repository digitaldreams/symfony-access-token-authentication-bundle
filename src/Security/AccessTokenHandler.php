<?php

namespace AccessToken\Security;

use AccessToken\Persistence\Repository\AccessTokenRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private AccessTokenRepository $repository,
        private ParameterBagInterface $parameterBag,
        private TranslatorInterface $translator
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        try {
            $decoded = JWT::decode(
                $accessToken,
                new Key($this->parameterBag->get('jwt.secret'), $this->parameterBag->get('jwt.algorithm'))
            );

            if ((new \DateTimeImmutable())->setTimestamp($decoded->exp) < new \DateTimeImmutable()) {
                throw new CustomUserMessageAuthenticationException(
                    $this->translator->trans('token.expired', domain: 'AccessTokenBundle')
                );
            }

            $accessToken = $this->repository->findOneByToken($accessToken);
            if (null === $accessToken || !$accessToken->isValid()) {
                throw new BadCredentialsException(
                    $this->translator->trans('token.invalid', domain: 'AccessTokenBundle')
                );
            }
            // and return a UserBadge object containing the user identifier from the found token
            $user = $accessToken->getUser();
            return new UserBadge($user?->getUserIdentifierValue());
        } catch (\UnexpectedValueException $e) {
            throw new CustomUserMessageAuthenticationException(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            throw new BadCredentialsException($e->getMessage());
        }
    }
}