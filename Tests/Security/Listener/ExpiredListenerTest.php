<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 17:37
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\Listener;

use Vss\OAuthExtensionBundle\Security\Listener\ExpiredListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Vss\OAuthExtensionBundle\Security\RoleAuth\Token\RoleToken;
use Vss\OAuthExtensionBundle\Security\Utils\ConfigProvider;

class ExpiredListenerTest extends \PHPUnit_Framework_TestCase
{

    public function testWrongToken() {

        $tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configProviderMock = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $listener = new ExpiredListener($tokenStorageMock, $configProviderMock);

        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $listener->onKernelRequest($eventMock);

    }
    public function testNotExpiredToken() {

        $tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configProviderMock = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();


        $listener = new ExpiredListener($tokenStorageMock, $configProviderMock);

        $tokenMock = $this->getMockBuilder(RoleToken::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tokenMock->expects($this->once())
            ->method('isExpired')
            ->willReturn(false);

        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenMock);

        $listener->onKernelRequest($eventMock);

    }
    public function testExpiredToken() {

        $tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configProviderMock = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();


        $listener = new ExpiredListener($tokenStorageMock, $configProviderMock);

        $tokenMock = $this->getMockBuilder(RoleToken::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tokenMock->expects($this->once())
            ->method('isExpired')
            ->willReturn(true);

        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($tokenMock);

        $configProviderMock->expects($this->once())
            ->method('getRoleConfig')
            ->willReturn(['logout_path' => '/logout']);

        $eventMock->expects($this->once())
            ->method('setResponse')
            ->willReturn(null);

        $listener->onKernelRequest($eventMock);

    }

}