<?php

namespace AccessToken\Services;

class JwtCredentialsRequest
{
    public function __construct(
        public readonly string $secret,
        public readonly string $issuer,
        public readonly int|string $subject,
        public readonly string $algorithm,
        public readonly \DateTimeImmutable $expireAt
    ) {
    }
}