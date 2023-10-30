<?php

namespace AccessToken\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CreateJwtTokenService
{
    public function execute(JwtCredentialsRequest $request)
    {
        $expireAt = (new \DateTimeImmutable('+24 hours'));
        $payload = [
            'iss' => $request->issuer,
            'sub' => $request->subject,
            'iat' => (new \DateTimeImmutable())->getTimestamp(),
            'exp' => $expireAt->getTimestamp()
        ];
        $jwtToken = JWT::encode(
            $payload,
            $request->secret,
            $request->algorithm
        );

        return $jwtToken;
    }
}