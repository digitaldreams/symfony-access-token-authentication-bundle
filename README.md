# Symfony Access Token Bundle
## Installation
```
composer require digitaldream/symfony-access-token
```
## Setup
**Step 1:**

copy `config/packages/access_token.yaml` from vendor folder

**Step 2**:

Copy `config/routes/access_token.yaml` from vendor folder

**Step 3**

Add these environment variables to your .env file
```dotenv
JWT_SECRET="YourSecretKey"
JWT_KEY=
JWT_ISSUER=localhost:8000
JWT_ALGORITHM=HS256
JWT_EXPIRE_AT='+24 hours'
```

**Step 4**

```yaml
security:
    firewalls:
        api:
            pattern: ^/api
            provider: app_user_provider #your user provider
            stateless: true
            user_checker: AccessToken\Security\UserChecker
            access_token:
                token_handler: AccessToken\Security\AccessTokenHandler
                failure_handler: AccessToken\Security\AuthenticationFailureHandler
    access_control:
      - { path: ^/api, roles: ROLE_USER } # Change this line according to your project  USER ROlES

```


### Calling the Login API
 ```javascript
fetch('/api/login',{
    body: {
        username: 'john@example.com',
        password: 'YourPassword'
    }
})
```

You can create your own login route. Just remove package route and use `AccessToken\Services\CreateAccessTokenService`

```php
namespace App\Controller

use AccessToken\Entity\AccessToken;
use AccessToken\Services\CreateAccessTokenService;
use AccessToken\Services\UserCredentialsRequest;
use Symfony\Component\HttpFoundation\Request;

class LoginController
{
    public function __construct(private  CreateAccessTokenService $accessTokenService) {}
    
    public function login(Request $request): 
    {
        //Write your logic
        //@var AccessToken $accessToken
      $accessToken=  $this->accessTokenService->execute(new UserCredentialsRequest('YourEmail@example.com','YourPassword'))
    }
}
```

__Enjoy!!!__

## Implement User verification and active feature
It will never generate a access token is user need to be email verified or inactive.
Simply implement the `AccessToken\Entity\TokenUserInterface` on your `User` Entity like below

```php

class User implements UserInterface, PasswordAuthenticatedUserInterface, TokenUserInterface
{
    
    public function isVerified(): ?bool
    {
       // return null if you don't have this functionality
        return true;
    }

    public function isActive(): ?bool
    {
        // return null if you don't have this functionality
        return true;
    }

    public function getUserIdentifierValue(): string
    {
        return $this->email;
    }

    public function getPublicId(): string
    {
        // It safe to use a UID (symfony UID) for generating JWT token. Do not expose your internal primary key
        return (string)$this->id;
    }
}
```

### Revoke Access Token
If you want to revoke all of the access token for a particular user then fire `AccessToken\Events\RevokeAccessTokensEvent`

```php
namespace App\Controller;

use AccessToken\Events\RevokeAccessTokensEvent;
use \Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SomeController {
 public function someAction(EventDispatcherInterface $dispatcher){
    //Do something with the User. E.g block or inactive or subscription expired.
    $dispatcher->dispatch(new RevokeAccessTokensEvent(1),RevokeAccessTokensEvent::NAME)
    }
}

```