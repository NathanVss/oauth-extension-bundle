<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 18/05/16
 * Time: 00:40
 */

namespace Vss\OAuthExtensionBundle\Security\EmailAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;
use Vss\OAuthExtensionBundle\Security\Exception\BadAuthentificationException;
use Vss\OAuthExtensionBundle\Security\RoleAuth\Token\RoleToken;
use Vss\OAuthExtensionBundle\Security\Utils\ConfigProvider;

/**
 * Class EmailAuthenticator
 * @package Vss\OAuthExtensionBundle\Security\RoleAuth
 */
class EmailAuthenticator implements SimpleFormAuthenticatorInterface {

    /**
     * @var EmailTokenProvider
     */
    private $emailTokenProvider;

    /**
     * @var array
     */
    private $config;

    /**
     * EmailAuthenticator constructor.
     * @param EmailTokenProvider $emailTokenProvider
     * @internal param RoleTokenProvider $roleTokenProvider
     */
    public function __construct(array $config, EmailTokenProvider $emailTokenProvider) {
        $this->config = $config;
        $this->emailTokenProvider = $emailTokenProvider;
    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     * @return UsernamePasswordToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        $user = $userProvider->loadUserByUsername($token->getUsername());
        
        $params = [
            "client_id" => $this->config['client_id'],
            "client_secret" => $this->config['client_secret'],
            "email" => $token->getUsername(),
            "password" => $token->getCredentials()
        ];

        try {
            $storage = $this->emailTokenProvider->authentificate($this->config['endpoint'], $params, $this->config['grant']);
        } catch (BadAuthentificationException $e) {
            // CAUTION: this message will be returned to the client
            // (so don't put any un-trusted messages / error strings here)
            throw new CustomUserMessageAuthenticationException('Invalid credentials');
        }

        $emailToken = new EmailToken($user, $user->getPassword(), $providerKey, $user->getRoles());
        $emailToken->setAccessToken($storage['accessToken']);
        $emailToken->setRefreshToken($storage['refreshToken']);
        $emailToken->setExpiresIn($storage['expiresIn']);
        return $emailToken;
    }

    /**
     * @param TokenInterface $token
     * @param $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof EmailToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request $request
     * @param $username
     * @param $password
     * @param $providerKey
     * @return UsernamePasswordToken
     */
    public function createToken(Request $request, $username, $password, $providerKey) {
        return new EmailToken($username, $password, $providerKey);
    }
}