<?php
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

/**
 * Class RoleGrant
 * @package Vss\OAuthExtensionBundle\Grant
 */
class RoleGrant implements GrantExtensionInterface
{

    /**
     * @var OAuthStorage
     */
    private $storage;

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy ;

    /**
     * RoleGrant constructor.
     * @param OAuthStorage $storage
     * @param RoleHierarchyInterface $roleHierarchy
     */
    public function __construct(OAuthStorage $storage, RoleHierarchyInterface $roleHierarchy) {
        $this->storage = $storage;
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * Returns true if $user is granted $requiredRole
     * @param $requiredRole
     * @param UserInterface $user
     * @return bool
     */
    public function isGranted($requiredRole, UserInterface $user) {

        $requiredRole = new Role($requiredRole);

        foreach ($user->getRoles() as $role) {
            $hierarchy = $this->roleHierarchy->getReachableRoles([new Role($role)]);
            if (in_array($requiredRole, $hierarchy)) {
                return true;
            }
        }
        return false;
    }

    /**
     * This authentification is role based
     * @see OAuth2\IOAuth2GrantExtension::checkGrantExtension
     */
    public function checkGrantExtension(IOAuth2Client $client, array $inputData, array $authHeaders)
    {

        if (!isset($inputData['username'])) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, 'No "username" parameter found');
        }
        if (!isset($inputData['password'])) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, 'No "password" parameter found');
        }
        if (!isset($inputData['required_role'])) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_REQUEST, 'No "required_role" parameter found');
        }

        $username = $inputData['username'];
        $password = $inputData['password'];
        $role = $inputData['required_role'];

        $stored = $this->storage->checkUserCredentials($client, $username, $password);

        if ($stored === false) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_GRANT, "Invalid username and password combination");
        }

        $user = $stored['data'];

        if (!$this->isGranted($role, $user)) {
            throw new OAuth2ServerException(OAuth2::HTTP_BAD_REQUEST, OAuth2::ERROR_INVALID_GRANT, "User is not granted $role");
        }

        return [
            'data' => $user
        ];
    }
}
