<?php

namespace AccessToken\Security;

use AccessToken\Entity\TokenUserInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
        if ($user instanceof TokenUserInterface && $user->isVerified() === false) {
            throw new CustomUserMessageAccountStatusException('Your account is not verified yet.');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        //At this point user is successfully authenticated.At is about to consume APIs
        // Here you can do few things
        // 01. Do some Database Actions like incrementing API call.
        // 02. updating user last_login field etc.
        // 03. Even all a background job and calculate last login and today's login and give them a brief what is new
    }
}