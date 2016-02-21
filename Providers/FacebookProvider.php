<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 12:08
 */

namespace Vss\OAuthExtensionBundle\Providers;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FacebookProvider
 * @package Vss\OAuthExtensionBundle\Providers
 */
class FacebookProvider extends GenericOAuth2Provider
{

    protected function setupOptions(OptionsResolver $optionsResolver) {

        parent::setupOptions($optionsResolver);

        $optionsResolver->setDefaults([
            'token_url' => "https://graph.facebook.com/v2.3/oauth/access_token",
            'user_url' => "https://graph.facebook.com/v2.3/me"
        ]);

    }
}