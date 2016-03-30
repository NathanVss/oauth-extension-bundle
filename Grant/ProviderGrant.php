<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 18:36
 */

namespace Vss\OAuthExtensionBundle\Grant;

use FOS\OAuthServerBundle\Storage\GrantExtensionInterface;
use OAuth2\Model\IOAuth2Client;
use OAuth2\OAuth2ServerException;
use OAuth2\OAuth2;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use FOS\OAuthServerBundle\Storage\OAuthStorage;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vss\OAuthExtensionBundle\Security\OAuth\OAuthManager;
use Vss\OAuthExtensionBundle\Security\OAuth\Exception\ProviderNotExistsException;
use Vss\OAuthExtensionBundle\Security\OAuth\OAuthUserManagerInterface;
use Vss\OAuthExtensionBundle\Providers\Exception\FailExchangeCodeException;
use Vss\OAuthExtensionBundle\Providers\Exception\FailedToGetUserInfoException;

/**
 * Class ProviderGrant
 * @package Vss\OAuthExtensionBundle\Grant
 */
class ProviderGrant implements GrantExtensionInterface
{

    /**
     * @var OAuthManager
     */
    private $oauthManager;

    /**
     * @var OAuthUserManagerInterface
     */
    private $userManager;

    public function __construct(OAuthManager $oauthManager, OAuthUserManagerInterface $userManager) {
        $this->oauthManager = $oauthManager;
        $this->userManager = $userManager;
    }

    /**
     * This authentification is role based
     * @see OAuth2\IOAuth2GrantExtension::checkGrantExtension
     */
    public function checkGrantExtension(IOAuth2Client $client, array $inputData, array $authHeaders)
    {

        if (!(isset($inputData['code']) || isset($inputData['access_token']))) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, 'No "code" or "access_token" parameter found');
        }
        if (!isset($inputData['provider'])) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, 'No "provider" parameter found');
        }
        if (!isset($inputData['redirect_uri'])) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, 'No "redirect_uri" parameter found');
        }

        $providerName = $inputData['provider'];
        $code = isset($inputData['code']) ? $inputData['code'] : null;
        $redirectUri = $inputData['redirect_uri'];
        $accessToken = isset($inputData['access_token']) ? $inputData['access_token'] : null;

        try {
            $provider = $this->oauthManager->getProvider($providerName);
        } catch(ProviderNotExistsException $e) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, "The provider $providerName does not exists.");
        }

        if ($accessToken) {
            $tokenData = [
                'accessToken' => $accessToken
            ];
        } else {
            try {
                $tokenData = $provider->getTokenFromCode($code, $redirectUri, $inputData);
            } catch (FailExchangeCodeException $e) {
                throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, "Failed to exchange code : {$e->getMessage()}");
            }
        }


        try {
            $userInfo = $provider->getUserInformations($tokenData['accessToken']);
        } catch(FailedToGetUserInfoException $e) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, "Failed to get user informations : {$e->getMessage()}");
        }

        $user = $this->userManager->getUserFromOAuthResponse($providerName, $userInfo);

        $this->userManager->updateAccessToken($user, $providerName, $tokenData['accessToken']);

        return [
            'data' => $user
        ];
    }
}
