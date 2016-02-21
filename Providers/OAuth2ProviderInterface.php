<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 11:22
 */

namespace Vss\OAuthExtensionBundle\Providers;

/**
 * Interface OAuth2ProviderInterface
 * @package Vss\OAuthExtensionBundle\Providers
 */
interface OAuth2ProviderInterface
{

    /**
     * @param string $code
     * @param $redirectUri
     * @param array $inputData
     * @return array
     */
    public function getTokenFromCode($code, $redirectUri, array $inputData = array());

    /**
     * @param $token
     * @return array
     */
    public function getUserInformations($token);

}