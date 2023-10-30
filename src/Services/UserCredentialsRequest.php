<?php

namespace AccessToken\Services;

class UserCredentialsRequest
{
    public function __construct(public readonly string $username, public readonly string $password)
    {
    }
}