<?php

namespace AccessToken\Services;

use AccessToken\Entity\TokenUserInterface;
use AccessToken\Persistence\Repository\AccessTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateAccessTokenService
{
    public function __construct(
        private AccessTokenRepository $accessTokenRepository,
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        private UserPasswordHasherInterface $hasher,
        private TranslatorInterface $translator,
        private CreateJwtTokenService $jwtTokenService
    ) {
    }

    public function execute(UserCredentialsRequest $request)
    {
        $user = $this->getUser($request);


        if ($user && $this->hasher->isPasswordValid($user, $request->password)) {
            if ($user instanceof TokenUserInterface) {
                if ($user->isVerified() === false) {
                    throw new CustomUserMessageAccountStatusException(
                        $this->translator->trans('user.not_verified', domain: 'AccessTokenBundle')
                    );
                }
                if ($user->isActive() === false) {
                    throw new CustomUserMessageAccountStatusException(
                        $this->translator->trans('user.inactive', domain: 'AccessTokenBundle')
                    );
                }
            }


            $userId = method_exists($user, 'getPublicId') ? $user->getPublicId() : $user->getId();
            $jwtRequest = new JwtCredentialsRequest(
                $this->parameterBag->get('jwt.secret'),
                $this->parameterBag->get('jwt.issuer'),
                $userId,
                $this->parameterBag->get('jwt.algorithm'),
                new \DateTimeImmutable($this->parameterBag->get('jwt.expire_at')),
            );
            $jwtToken = $this->jwtTokenService->execute($jwtRequest);

            $accessToken = $this->accessTokenRepository->save($jwtToken, $user);

            return [
                'jwt' => $jwtToken
            ];
        }
        return [
            'error' => $this->translator->trans('user.not_found', domain: 'AccessTokenBundle')
        ];
    }

    protected function getUser($request): ?UserInterface
    {
        $userClass = $this->parameterBag->get('jwt.user_entity');
        $ref = new \ReflectionClass($userClass);
        $userEntity = $ref->newInstanceWithoutConstructor();

        if (!$userEntity instanceof UserInterface) {
            throw new CustomUserMessageAuthenticationException($this->translator->trans('user.wrong_entity'));
        }

        $userRepository = $this->entityManager->getRepository($userClass);

        if ($userRepository instanceof UserLoaderInterface) {
            $user = $userRepository->loadUserByIdentifier($request->username);
        } else {
            $identifierColumn = $userEntity->getUserIdentifier();
            $user = $userRepository->findOneBy([$identifierColumn => $request->username]);
        }

        return $user;
    }
}