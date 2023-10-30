<?php

namespace AccessToken\Controller;

use AccessToken\Entity\AccessToken;
use AccessToken\Persistence\Repository\AccessTokenRepository;
use AccessToken\Services\CreateAccessTokenService;
use AccessToken\Services\CreateJwtTokenService;
use AccessToken\Services\UserCredentialsRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiLoginController extends AbstractController
{
    public function __construct(
        private CreateAccessTokenService $accessTokenService
    ) {
    }

    #[Route('login', name: 'api_bundle_login', methods: ['POST'])]
    public function login(Request $request)
    {
        $response = $this->accessTokenService->execute(
            new UserCredentialsRequest($request->get('username'), $request->get('password'))
        );
        return new JsonResponse($response);
    }

}