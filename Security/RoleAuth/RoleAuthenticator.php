<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 31/01/16
 * Time: 12:17
 */
namespace Vss\OAuthExtensionBundle\Security\RoleAuth;

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
 * Class RoleAuthenticator
 * @package Vss\OAuthExtensionBundle\Security\RoleAuth
 */
class RoleAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var RoleTokenProvider
     */
    private $roleTokenProvider;

    /**
     * @var string
     */
    private $role;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * RoleAuthenticator constructor.
     * @param RoleTokenProvider $roleTokenProvider
     */
    public function __construct($role, RoleTokenProvider $roleTokenProvider , ConfigProvider $configProvider)
    {
        $this->role = $role;
        $this->roleTokenProvider = $roleTokenProvider;
        $this->configProvider = $configProvider;
    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     * @return UsernamePasswordToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $user = $userProvider->loadUserByUsername($token->getUsername());

        $config = $this->configProvider->getRoleConfig();

        $params = [
            "client_id" => $config['client_id'],
            "client_secret" => $config['client_secret'],
            "username" => $token->getUsername(),
            "password" => $token->getCredentials()
        ];

        try {
            $storage = $this->roleTokenProvider->authentificate($config['endpoint'], $params, $this->role);
        } catch(BadAuthentificationException $e) {
            // CAUTION: this message will be returned to the client
            // (so don't put any un-trusted messages / error strings here)
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }


        $roleToken = new RoleToken(
            $user,
            $user->getPassword(),
            $providerKey,
            $user->getRoles()
        );
        $roleToken->setAccessToken($storage['accessToken']);
        $roleToken->setRefreshToken($storage['refreshToken']);
        $roleToken->setRoleRequired($this->role);
        $roleToken->setExpiresIn($storage['expiresIn']);

        return $roleToken;
    }


    /**
     * @param TokenInterface $token
     * @param $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof RoleToken
            && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request $request
     * @param $username
     * @param $password
     * @param $providerKey
     * @return UsernamePasswordToken
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new RoleToken($username, $password, $providerKey);
    }
}