<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 23:48
 */

namespace Vss\OAuthExtensionBundle\Tests\Grant;

use Symfony\Component\Security\Core\User\UserInterface;
use Vss\OAuthExtensionBundle\Security\OAuth\OAuthManager;
use Vss\OAuthExtensionBundle\Security\OAuth\OAuthUserManagerInterface;
use Vss\OAuthExtensionBundle\Grant\ProviderGrant;
use OAuth2\Model\IOAuth2Client;
use Vss\OAuthExtensionBundle\Providers\OAuth2ProviderInterface;
use Vss\OAuthExtensionBundle\Security\OAuth\Exception\ProviderNotExistsException;
use Vss\OAuthExtensionBundle\Providers\Exception\FailExchangeCodeException;
use Vss\OAuthExtensionBundle\Providers\Exception\FailedToGetUserInfoException;

class ProviderGrantTest extends \PHPUnit_Framework_TestCase
{
    public function getClientMock() {
        return $this->getMockBuilder(IOAuth2Client::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCodeNotInParams() {

        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $oauthManager = $this->getMockBuilder(OAuthManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $oauthUserManager = $this->getMockBuilder(OAuthUserManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $providerGrant = new ProviderGrant($oauthManager, $oauthUserManager);

        $input = [
            'nothing' => 'here'
        ];
        $providerGrant->checkGrantExtension($this->getClientMock(), $input, []);

    }

    public function testProviderNotInParams() {
        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $oauthManager = $this->getMockBuilder(OAuthManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $oauthUserManager = $this->getMockBuilder(OAuthUserManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $providerGrant = new ProviderGrant($oauthManager, $oauthUserManager);

        $input = [
            'code' => 'code'
        ];
        $providerGrant->checkGrantExtension($this->getClientMock(), $input, []);

    }

    public function testProviderNotExists() {
        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $oauthManager = $this->getMockBuilder(OAuthManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $oauthManager->expects($this->once())
            ->method('getProvider')
            ->willThrowException(new ProviderNotExistsException());

        $oauthUserManager = $this->getMockBuilder(OAuthUserManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $providerGrant = new ProviderGrant($oauthManager, $oauthUserManager);

        $input = [
            'code' => 'code',
            'provider' => 'lul'
        ];
        $providerGrant->checkGrantExtension($this->getClientMock(), $input, []);

    }


    public function testFailGetToken() {
        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $oauthManager = $this->getMockBuilder(OAuthManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $provider = $this->getMockBuilder(OAuth2ProviderInterface::class)
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getTokenFromCode')
            ->with('code')
            ->willThrowException(new FailExchangeCodeException());

        $oauthManager->expects($this->once())
            ->method('getProvider')
            ->willReturn($provider);

        $oauthUserManager = $this->getMockBuilder(OAuthUserManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $providerGrant = new ProviderGrant($oauthManager, $oauthUserManager);

        $input = [
            'code' => 'code',
            'provider' => 'lul'
        ];
        $providerGrant->checkGrantExtension($this->getClientMock(), $input, []);

    }
    public function testFailGetUserInfos() {
        $this->setExpectedException('OAuth2\OAuth2ServerException');

        $oauthManager = $this->getMockBuilder(OAuthManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $provider = $this->getMockBuilder(OAuth2ProviderInterface::class)
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('getTokenFromCode')
            ->with('code')
            ->willReturn(['accessToken' => 'token']);

        $provider->expects($this->once())
            ->method('getUserInformations')
            ->with('token')
            ->willThrowException(new FailedToGetUserInfoException());

        $oauthManager->expects($this->once())
            ->method('getProvider')
            ->willReturn($provider);

        $oauthUserManager = $this->getMockBuilder(OAuthUserManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $providerGrant = new ProviderGrant($oauthManager, $oauthUserManager);

        $input = [
            'code' => 'code',
            'provider' => 'lul'
        ];
        $providerGrant->checkGrantExtension($this->getClientMock(), $input, []);

    }


    public function testSucceed() {

        $user = $this->getMockBuilder(UserInterface::class)
            ->getMock();

        $oauthManager = $this->getMockBuilder(OAuthManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $provider = $this->getMockBuilder(OAuth2ProviderInterface::class)
            ->getMock();

        $provider->expects($this->once())
            ->method('getTokenFromCode')
            ->with('code')
            ->willReturn(['accessToken' => 'token']);

        $provider->expects($this->once())
            ->method('getUserInformations')
            ->with('token')
            ->willReturn(['id' => 37]);

        $oauthManager->expects($this->once())
            ->method('getProvider')
            ->willReturn($provider);

        $oauthUserManager = $this->getMockBuilder(OAuthUserManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $oauthUserManager->expects($this->once())
            ->method('getUserFromOAuthResponse')
            ->with('facebook', ['id' => 37])
            ->willReturn($user);

        $oauthUserManager->expects($this->once())
            ->method('updateAccessToken')
            ->with($user, 'facebook', 'token');

        $providerGrant = new ProviderGrant($oauthManager, $oauthUserManager);

        $input = [
            'code' => 'code',
            'provider' => 'facebook'
        ];
        $output = $providerGrant->checkGrantExtension($this->getClientMock(), $input, []);

        $expects = [
            'data' => $user
        ];
        $this->assertEquals($expects, $output);
    }

}