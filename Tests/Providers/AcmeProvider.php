<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 13/02/16
 * Time: 20:39
 */

namespace Vss\OAuthExtensionBundle\Tests\Providers;

use Vss\OAuthExtensionBundle\Providers\GenericOAuth2Provider;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AcmeProvider extends GenericOAuth2Provider
{
    protected function setupOptions(OptionsResolver $optionsResolver) {

        parent::setupOptions($optionsResolver);

        $optionsResolver->setDefaults([
            'token_url' => "https://api.acme.com/access_token",
            'user_url' => "https://api.acme.com/me"
        ]);

    }

}