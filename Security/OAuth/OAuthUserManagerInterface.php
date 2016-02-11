<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 19:54
 */

namespace Vss\OAuthExtensionBundle\Security\OAuth;
use Symfony\Component\Security\Core\User\UserInterface;

interface OAuthUserManagerInterface
{

    /**
     * @param string $providerName
     * @param array $data
     * @return UserInterface
     */
    public function getUserFromOAuthResponse($providerName, array $data);

    /**
     * @param UserInterface $user
     * @param $providerName
     * @param $token
     */
    public function updateAccessToken(UserInterface $user, $providerName, $token);

}