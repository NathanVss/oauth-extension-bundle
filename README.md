VssOAuthExtensionBundle
====================

[![Build Status](https://travis-ci.org/NathanVss/oauth-extension-bundle.svg?branch=master)](https://travis-ci.org/NathanVss/oauth-extension-bundle)

[![codecov.io](https://codecov.io/github/NathanVss/oauth-extension-bundle/coverage.svg?branch=master)](https://codecov.io/github/NathanVss/oauth-extension-bundle?branch=master)

[![Latest Stable Version](https://poser.pugx.org/vss/oauth-extension-bundle/v/stable)](https://packagist.org/packages/vss/oauth-extension-bundle) [![Total Downloads](https://poser.pugx.org/vss/oauth-extension-bundle/downloads)](https://packagist.org/packages/vss/oauth-extension-bundle) [![Latest Unstable Version](https://poser.pugx.org/vss/oauth-extension-bundle/v/unstable)](https://packagist.org/packages/vss/oauth-extension-bundle) [![License](https://poser.pugx.org/vss/oauth-extension-bundle/license)](https://packagist.org/packages/vss/oauth-extension-bundle)

## Documentation

### Introduction

This bundle extends the features of the `friendsofsymfony/oauth-server-bundle` ( https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/blob/master/Resources/doc/index.md ).

### Grants

#### Email Grant

This grant allow email based token delivering, the first argument must be a self-made class implementing the interface `Vss\OAuthExtensionBundle\Security\Utils\EmailProvider`. This class is responsible to deliver a user ( `Symfony\Component\Security\Core\User\UserInterface`) or null. 


```yaml
  oauth.grant.email:
      class: Vss\OAuthExtensionBundle\Grant\EmailGrant
      arguments:
          - "@app.security.provider.client_email"
          - "@security.encoder_factory"
      tags:
            - { name: fos_oauth_server.grant_extension, uri: 'http://mplatform.com/grants/email' }

```

Here is the `@app.security.provider.client_email`service, `Client` implements `UserInterface`.

``` php
namespace AppBundle\Security\Client;

use AppBundle\Entity\Client;
use AppBundle\Entity\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Vss\OAuthExtensionBundle\Security\Utils\EmailProvider;

/**
 * Class ClientEmailProvider
 * @package AppBundle\Security
 */
class ClientEmailProvider implements EmailProvider {

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $rep;

    /**
     * ClientEmailProvider constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->rep = $em->getRepository('AppBundle:Client');
    }

    /**
     * @inheritdoc
     */
    public function loadByEmail($email){
        return $this->rep->findOneBy(['email' => $email]);
    }
}

```

The idea is that you can setup multiple grants for differents user types, we can imagine one EmailProvider returning Admin, an other returning a User, Moderator, etc ... So it is very flexible.

#### Provider Grant

This grant is supposed to deliver a token from a OAuth Login, like Facebook Login.

``` yaml

    oauth.grant.provider:
        class: Vss\OAuthExtensionBundle\Grant\ProviderGrant
        arguments: ["@vss_oauth_extension.oauth_manager", "@app.security.client_manager"]
        tags:
              - { name: fos_oauth_server.grant_extension, uri: 'http://mplatform.com/grants/provider' }
```

The second argument implement the interface `Vss\OAuthExtensionBundle\Security\OAuth\OAuthUserManagerInterface`.
Then, this manage should return a user with the provider id or email for example.
This grant works with OAuth Authorization Code or OAuth Access Token from the provider. If it is possible I would recommend using the Code instead of Access Token if your website doesn't support HTTPS.

Now, only these providers are available :
- Facebook

Example configuration from `config.yml`.

``` yaml
vss_oauth_extension:

    providers:
        facebook:
            type: facebook
            client_id: 1746857792272443
            client_secret: cf395620ca4e0622af55c6d709148f72
```

### TODOS

Improve doc, more tests.
