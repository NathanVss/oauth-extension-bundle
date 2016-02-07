<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 17:56
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Vss\OAuthExtensionBundle\Security\RoleAuth\Token\RoleToken;
use Vss\OAuthExtensionBundle\Security\Listener\KernelResponseListener;
use Symfony\Component\HttpFoundation\Response;

class KernelResponseListenerTest extends \PHPUnit_Framework_TestCase
{


    public function testWrongStatus() {

        $tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $listener = new KernelResponseListener($tokenStorageMock);

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(500);

        $eventMock = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->expects($this->once())
            ->method('getResponse')
            ->willReturn($responseMock);

        $listener->onKernelResponse($eventMock);

    }

    public function testWrongToken() {

        $tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $listener = new KernelResponseListener($tokenStorageMock);

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $eventMock = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->expects($this->once())
            ->method('getResponse')
            ->willReturn($responseMock);

        $listener->onKernelResponse($eventMock);

    }
    public function testCorrectToken() {

        $tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder(RoleToken::class)
            ->disableOriginalConstructor()
            ->getMock();

        $token->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('access_token');
        $token->expects($this->once())
            ->method('getRefreshToken')
            ->willReturn('refresh_token');

        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $listener = new KernelResponseListener($tokenStorageMock);

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $responseMock->expects($this->once())
            ->method('getContent')
            ->willReturn('<head></head>');

        $eventMock = $this->getMockBuilder(FilterResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->expects($this->any())
            ->method('getResponse')
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method('setContent')
            ->willReturnCallback(function($content) {

                $this->assertContains('authBootstrap = {', $content);

            });

        $listener->onKernelResponse($eventMock);

    }


}