# Symfony Access Token Bundle
## Installation
```
composer require digitaldreams/symfony-access-token
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

__Enjoy!!!__ 


## Implement Interface
It will never generate a access token is user need to be email verified or inactive.
Simply implement the `AccessToken\Entity\TokenUserInterface` on your `User` Entity like below

```php

class User implements UserInterface, PasswordAuthenticatedUserInterface, TokenUserInterface
{
    
    public function isVerified(): ?bool
    {
       // return null is you do have this functionality
        return true;
    }

    public function isActive(): ?bool
    {
        // return null is you do have this functionality
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
## features
