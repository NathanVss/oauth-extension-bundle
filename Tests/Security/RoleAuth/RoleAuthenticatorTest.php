<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 19:04
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\RoleAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;
use Vss\OAuthExtensionBundle\Security\Exception\BadAuthentificationException;
use Vss\OAuthExtensionBundle\Security\RoleAuth\Token\RoleToken;
use Vss\OAuthExtensionBundle\Security\Utils\ConfigProvider;
use Vss\OAuthExtensionBundle\Security\RoleAuth\RoleAuthenticator;
use Vss\OAuthExtensionBundle\Security\RoleAuth\RoleTokenProvider;

class RoleAuthenticatorTest extends \PHPUnit_Framework_TestCase
{

    public function testToken() {

        $roleTokenProvider = $this->getMockBuilder(RoleTokenProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $roleAuthenticator = new RoleAuthenticator('ROLE_ADMIN', $roleTokenProvider, $configProvider);

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $token = $roleAuthenticator->createToken($requestMock, 'admin', 'secret', 37);

        $this->assertTrue($roleAuthenticator->supportsToken($token, 37));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException
     */
    public function testFail() {

        $roleTokenProvider = $this->getMockBuilder(RoleTokenProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $roleTokenProvider->expects($this->once())
            ->method('authentificate')
            ->willThrowException(new BadAuthentificationException());

        $configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configProvider->expects($this->once())
            ->method('getRoleConfig')
            ->willReturn([
                'client_id' => 'client_id',
                'client_secret' => 'secret',
                'endpoint' => '/token'
            ]);

        $roleAuthenticator = new RoleAuthenticator('ROLE_ADMIN', $roleTokenProvider, $configProvider);

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMock();
        $tokenMock->expects($this->any())->method('getUsername')->willReturn('admin');
        $tokenMock->expects($this->once())->method('getCredentials')->willReturn('secret');

        $userMock = $this->getMockBuilder(UserInterface::class)
            ->getMock();

        $userProviderMock = $this->getMockBuilder(UserProviderInterface::class)
            ->getMock();

        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->willReturn($userMock);

        $roleAuthenticator->authenticateToken($tokenMock, $userProviderMock, 37);

    }

    public function testSuccess() {

        $roleTokenProvider = $this->getMockBuilder(RoleTokenProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $roleTokenProvider->expects($this->once())
            ->method('authentificate')
            ->willReturn([
                'accessToken' => 'token',
                'refreshToken' => 'refresh',
                'expiresIn' => 3600
            ]);

        $configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configProvider->expects($this->once())
            ->method('getRoleConfig')
            ->willReturn([
                'client_id' => 'client_id',
                'client_secret' => 'secret',
                'endpoint' => '/token'
            ]);

        $roleAuthenticator = new RoleAuthenticator('ROLE_ADMIN', $roleTokenProvider, $configProvider);

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMock();
        $tokenMock->expects($this->any())->method('getUsername')->willReturn('admin');
        $tokenMock->expects($this->once())->method('getCredentials')->willReturn('secret');

        $userMock = $this->getMockBuilder(UserInterface::class)
            ->getMock();
        $userMock->expects($this->once())->method('getPassword')->willReturn('');
        $userMock->expects($this->once())->method('getRoles')->willReturn(['ROLE_ADMIN']);

        $userProviderMock = $this->getMockBuilder(UserProviderInterface::class)
            ->getMock();

        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->willReturn($userMock);

        $roleToken = $roleAuthenticator->authenticateToken($tokenMock, $userProviderMock, 37);
        $this->assertTrue($roleToken instanceof RoleToken);
    }

}