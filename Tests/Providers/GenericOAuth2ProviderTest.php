<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 13/02/16
 * Time: 20:36
 */

namespace Vss\OAuthExtensionBundle\Tests\Providers;

use Vss\OAuthExtensionBundle\Security\Utils\BrowserManager;
use Buzz\Browser;
use Buzz\Message\Response;
use Vss\OAuthExtensionBundle\Providers\Exception\FailExchangeCodeException;
use Vss\OAuthExtensionBundle\Providers\Exception\FailedToGetUserInfoException;

class GenericOAuth2ProviderTest extends \PHPUnit_Framework_TestCase
{

    public function getAcmeProvider() {

        $browserManager = $this->getMockBuilder(BrowserManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $options = [
            'client_id' => 'me',
            'client_secret' => 'secret',
            'redirect_uri' => 'http://localhost/',
        ];

        $provider = new AcmeProvider($browserManager, $options, 'acme');

        $this->assertEquals('acme', $provider->getName());

        return ['provider' => $provider, 'browser' => $browserManager];
    }

    public function testGetUserInfosWrongStatusCodeResponseAndValidParams() {
        $this->setExpectedException('Vss\OAuthExtensionBundle\Providers\Exception\FailedToGetUserInfoException');

        $data = $this->getAcmeProvider();
        $provider = $data['provider'];
        $browserManager = $data['browser'];

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();


        $browserManager->expects($this->once())
            ->method('getBrowser')
            ->willReturn($browser);

        $browser->expects($this->once())
            ->method('get')
            ->willReturnCallback(function($uri) use ($response) {

                $this->assertContains("https://api.acme.com/me", $uri);
                $this->assertContains('access_token=toktok', $uri);

                return $response;
            });

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(500);

        $params = $provider->getUserInformations('toktok');
    }
    public function testGetUserInfosCantReadResponse() {
        $this->setExpectedException('Vss\OAuthExtensionBundle\Providers\Exception\FailedToGetUserInfoException');

        $data = $this->getAcmeProvider();
        $provider = $data['provider'];
        $browserManager = $data['browser'];

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();


        $browserManager->expects($this->once())
            ->method('getBrowser')
            ->willReturn($browser);

        $browser->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $response->expects($this->any())
            ->method('getContent')
            ->willReturn('[this in not json]');

        $params = $provider->getUserInformations('toktok');
    }

    public function testGetUserInfosSuccess() {
        $data = $this->getAcmeProvider();
        $provider = $data['provider'];
        $browserManager = $data['browser'];

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();


        $browserManager->expects($this->once())
            ->method('getBrowser')
            ->willReturn($browser);

        $browser->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $content = [
            'id' => '37',
            'name' => 'Nathan Vasse'
        ];

        $response->expects($this->any())
            ->method('getContent')
            ->willReturn(json_encode($content));

        $params = $provider->getUserInformations('toktok');

        $this->assertTrue($params['id'] == '37');
    }


    public function testGetTokenFromCodeWrongStatusCodeResponseAndValidParams() {
        $this->setExpectedException('Vss\OAuthExtensionBundle\Providers\Exception\FailExchangeCodeException');

        $data = $this->getAcmeProvider();
        $provider = $data['provider'];
        $browserManager = $data['browser'];

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();


        $browserManager->expects($this->once())
            ->method('getBrowser')
            ->willReturn($browser);

        $browser->expects($this->once())
            ->method('get')
            ->willReturnCallback(function($uri) use ($response) {

                $this->assertContains("https://api.acme.com/access_token", $uri);
                $this->assertContains('client_id=me', $uri);
                $this->assertContains('client_secret=secret', $uri);
                $this->assertContains('redirect_uri=http', $uri);
                $this->assertContains('code=code', $uri);

                return $response;
            });

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(500);

        $params = $provider->getTokenFromCode('code');
    }
    public function testGetTokenFromCodeCantReadResponse() {
        $this->setExpectedException('Vss\OAuthExtensionBundle\Providers\Exception\FailExchangeCodeException');

        $data = $this->getAcmeProvider();
        $provider = $data['provider'];
        $browserManager = $data['browser'];

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();


        $browserManager->expects($this->once())
            ->method('getBrowser')
            ->willReturn($browser);

        $browser->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $response->expects($this->any())
            ->method('getContent')
            ->willReturn('[this in not json]');

        $params = $provider->getTokenFromCode('code');
    }

    public function testGetTokenFromCodeValidResponse() {

        $data = $this->getAcmeProvider();
        $provider = $data['provider'];
        $browserManager = $data['browser'];

        $browser = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();


        $browserManager->expects($this->once())
            ->method('getBrowser')
            ->willReturn($browser);

        $browser->expects($this->once())
            ->method('get')
            ->willReturn($response);

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $content = [
            'access_token' => 'trolol',
            'expires_in' => '3600'
        ];

        $response->expects($this->any())
            ->method('getContent')
            ->willReturn(json_encode($content));

        $params = $provider->getTokenFromCode('code');

        // Params names are converted to universal ones due to $format attribute ( access_token => accessToken )
        $this->assertTrue($params['accessToken'] == 'trolol');
        $this->assertTrue($params['expiresIn'] == '3600');
    }

}