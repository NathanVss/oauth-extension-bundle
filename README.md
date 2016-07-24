VssOAuthExtensionBundle
====================

[![Build Status](https://travis-ci.org/NathanVss/oauth-extension-bundle.svg?branch=master)](https://travis-ci.org/NathanVss/oauth-extension-bundle)

[![codecov.io](https://codecov.io/github/NathanVss/oauth-extension-bundle/coverage.svg?branch=master)](https://codecov.io/github/NathanVss/oauth-extension-bundle?branch=master)

[![Latest Stable Version](https://poser.pugx.org/vss/oauth-extension-bundle/v/stable)](https://packagist.org/packages/vss/oauth-extension-bundle) [![Total Downloads](https://poser.pugx.org/vss/oauth-extension-bundle/downloads)](https://packagist.org/packages/vss/oauth-extension-bundle) [![Latest Unstable Version](https://poser.pugx.org/vss/oauth-extension-bundle/v/unstable)](https://packagist.org/packages/vss/oauth-extension-bundle) [![License](https://poser.pugx.org/vss/oauth-extension-bundle/license)](https://packagist.org/packages/vss/oauth-extension-bundle)

## Documentation

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

