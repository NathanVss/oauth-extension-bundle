<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 28/01/16
 * Time: 11:31
 */

namespace Vss\OAuthExtensionBundle\Test\Grant;

use Vss\OAuthExtensionBundle\Grant\RoleGrant;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\OAuthServerBundle\Storage\OAuthStorage;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Role\Role;
use OAuth2\Model\IOAuth2Client;

class RoleGrantTest extends \PHPUnit_Framework_TestCase
{

    public function getClientMock() {
        return $this->getMockBuilder(IOAuth2Client::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function getAdminMock() {
        $user = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $user->expects($this->any())
            ->method('getRoles')
            ->willReturn(['ROLE_ADMIN']);

        return $user;
    }

    public function getHierarchyMock() {
        $roleHierarchy = $this->getMockBuilder(RoleHierarchyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $roleHierarchy->expects($this->any())
            ->method('getReachableRoles')
            ->willReturn([
                new Role('ROLE_ADMIN'),
                new Role('ROLE_USER')
            ]);

        return $roleHierarchy;
    }

    public function testSuccess() {

        $storage = $this->getMockBuilder(OAuthStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $admin = $this->getAdminMock();

        $storage->expects($this->once())
            ->method('checkUserCredentials')
            ->willReturn([
                'data' => $admin
            ]);

        $roleHierarchy = $this->getHierarchyMock();

        $grant = new RoleGrant($storage, $roleHierarchy);

        $input = [
            'username' => 'shark',
            'password' => 'password',
            'required_role' => 'ROLE_ADMIN'
        ];
        $response = $grant->checkGrantExtension($this->getClientMock(), $input, []);

        $this->assertEquals($response, [
            'data' => $admin
        ]);

    }

    public function testNotGranted() {
        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $storage = $this->getMockBuilder(OAuthStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $admin = $this->getAdminMock();

        $storage->expects($this->once())
            ->method('checkUserCredentials')
            ->willReturn([
                'data' => $admin
            ]);

        $roleHierarchy = $this->getHierarchyMock();

        $grant = new RoleGrant($storage, $roleHierarchy);

        $input = [
            'username' => 'shark',
            'password' => 'password',
            'required_role' => 'ROLE_SUPER_ADMIN'
        ];
        $grant->checkGrantExtension($this->getClientMock(), $input, []);

    }


    public function testBadCredentials() {
        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $storage = $this->getMockBuilder(OAuthStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects($this->once())
            ->method('checkUserCredentials')
            ->willReturn(false);

        $roleHierarchy = $this->getHierarchyMock();

        $grant = new RoleGrant($storage, $roleHierarchy);

        $input = [
            'username' => 'shark',
            'password' => 'password',
            'required_role' => 'ROLE_ADMIN'
        ];
        $grant->checkGrantExtension($this->getClientMock(), $input, []);
    }

    public function testMissingUsername() {
        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $storage = $this->getMockBuilder(OAuthStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $roleHierarchy = $this->getHierarchyMock();

        $grant = new RoleGrant($storage, $roleHierarchy);

        $input = [
            'password' => 'password',
            'required_role' => 'ROLE_ADMIN'
        ];
        $grant->checkGrantExtension($this->getClientMock(), $input, []);
    }
    public function testMissingPassword() {
        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $storage = $this->getMockBuilder(OAuthStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $roleHierarchy = $this->getHierarchyMock();

        $grant = new RoleGrant($storage, $roleHierarchy);

        $input = [
            'username' => 'shark',
            'required_role' => 'ROLE_ADMIN'
        ];
        $grant->checkGrantExtension($this->getClientMock(), $input, []);
    }

    public function testMissingRole() {
        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $storage = $this->getMockBuilder(OAuthStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $roleHierarchy = $this->getHierarchyMock();

        $grant = new RoleGrant($storage, $roleHierarchy);

        $input = [
            'username' => 'shark',
            'password' => 'secret'
        ];
        $grant->checkGrantExtension($this->getClientMock(), $input, []);
    }
}