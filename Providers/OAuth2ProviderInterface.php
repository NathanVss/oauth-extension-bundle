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
     * @return array
     */
    public function getTokenFromCode($code);

    /**
     * @param $token
     * @return array
     */
    public function getUserInformations($token);

}