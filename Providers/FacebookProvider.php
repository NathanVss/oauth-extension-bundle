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

    /**
     * @inheritdoc
     */
    public function getUserInformations($accessToken) {

        $browser = $this->browserManager->getBrowser();;

        $params = [
            $this->format['token'] => $accessToken,
            'fields' => 'id,name,email'
        ];
        $response = $browser->get($this->options['user_url'] . '?' . http_build_query($params));

        return $this->handleUserInformationsResponse($response);
    }

    protected function setupOptions(OptionsResolver $optionsResolver) {

        parent::setupOptions($optionsResolver);

        $optionsResolver->setDefaults([
            'token_url' => "https://graph.facebook.com/v2.3/oauth/access_token",
            'user_url' => "https://graph.facebook.com/v2.3/me"
        ]);

    }
}