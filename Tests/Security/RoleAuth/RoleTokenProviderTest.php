<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 18:30
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\RoleAuth;

use Symfony\Component\Security\Core\Role\Role;
use Vss\OAuthExtensionBundle\Security\RoleAuth\RoleTokenProvider;
use Buzz\Message\Response;
use Buzz\Browser;
use Vss\OAuthExtensionBundle\Security\Utils\BrowserManager;
use Vss\OAuthExtensionBundle\Security\Exception\BadAuthentificationException;

class RoleTokenProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testHandleResponseSuccess()
    {

        $browserManager = $this->getMockBuilder(BrowserManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();


        $provider = new RoleTokenProvider($browserManager);

        $content = ['success' => 'yep'];

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $responseMock->expects($this->once())
            ->method('getContent')
            ->willReturn(json_encode($content));

        $this->assertEquals($content, $provider->handleResponse($responseMock));

    }

    /**
     * @expectedException \Vss\OAuthExtensionBundle\Security\Exception\BadAuthentificationException
     */
    public function testHandleResponseUndefinedError() {

        $browserManager = $this->getMockBuilder(BrowserManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $provider = new RoleTokenProvider($browserManager);

        $content = null;

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(401);

        $responseMock->expects($this->once())
            ->method('getContent')
            ->willReturn(json_encode($content));

        $provider->handleResponse($responseMock);
    }

    /**
     * @expectedException \Vss\OAuthExtensionBundle\Security\Exception\BadAuthentificationException
     */
    public function testAuthenticateFail() {

        $content = ['error' => 'invalid_credentials', 'error_description' => 'wrong password username combination'];

        $browserMock = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(401);

        $responseMock->expects($this->once())
            ->method('getContent')
            ->willReturn(json_encode($content));


        $browserMock->expects($this->once())
            ->method('get')
            ->willReturn($responseMock);

        $browserManager = $this->getMockBuilder(BrowserManager::class)
            ->disableOriginalConstructor()
            ->getMock();


        $browserManager->expects($this->once())
            ->method('getBrowser')
            ->willReturn($browserMock);


        $provider = new RoleTokenProvider($browserManager);


        $provider->authentificate('/token', [], 'ROLE_ADMIN');
    }


    public function testAuthenticateSuccess() {

        $content = [
            'access_token' => 'access_token',
            'refresh_token' => 'refresh_token',
            'expires_in' => 3600
        ];

        $browserMock = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $responseMock->expects($this->once())
            ->method('getContent')
            ->willReturn(json_encode($content));


        $browserMock->expects($this->once())
            ->method('get')
            ->willReturn($responseMock);

        $browserManager = $this->getMockBuilder(BrowserManager::class)
            ->disableOriginalConstructor()
            ->getMock();


        $browserManager->expects($this->once())
            ->method('getBrowser')
            ->willReturn($browserMock);


        $provider = new RoleTokenProvider($browserManager);


        $data = $provider->authentificate('/token', [], 'ROLE_ADMIN');

        $this->assertEquals($content['access_token'], $data['accessToken']);
    }


}