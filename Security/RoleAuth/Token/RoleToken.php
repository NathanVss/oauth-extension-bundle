<?php
namespace Vss\OAuthExtensionBundle\Security\RoleAuth\Token;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class RoleToken
 * @package Vss\OAuthExtensionBundle\Security\RoleAuth\Token
 */
class RoleToken extends UsernamePasswordToken
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var int
     */
    private $createdAt;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @var string
     */
    private $roleRequired;

    /**
     * RoleToken constructor.
     * @param object|string $user
     * @param string $credentials
     * @param string $providerKey
     * @param array $roles
     */
    public function __construct($user, $credentials, $providerKey, array $roles = array()) {

        parent::__construct($user, $credentials, $providerKey, $roles);
        $this->createdAt = time();

    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->accessToken,
            $this->refreshToken,
            $this->createdAt,
            $this->expiresIn,
            $this->roleRequired,
            parent::serialize()
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->accessToken,
            $this->refreshToken,
            $this->createdAt,
            $this->expiresIn,
            $this->roleRequired,
            $parent
        ) = $data;

        parent::unserialize($parent);
    }

    public function isExpired() {
        // 5 minutes delay
        $delay = 60*5;
        return time() >= $this->createdAt + $this->expiresIn + $delay;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param mixed $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }



    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param mixed $expiresIn
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
    }

    /**
     * @return mixed
     */
    public function getRoleRequired()
    {
        return $this->roleRequired;
    }

    /**
     * @param mixed $roleRequired
     */
    public function setRoleRequired($roleRequired)
    {
        $this->roleRequired = $roleRequired;
    }

    /**
     * @return array|\string[]|\Symfony\Component\Security\Core\Role\RoleInterface[]
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param array|\string[]|\Symfony\Component\Security\Core\Role\RoleInterface[] $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }


}