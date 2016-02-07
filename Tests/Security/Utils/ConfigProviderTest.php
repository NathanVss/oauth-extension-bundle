<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 18:17
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\Utils;

use Vss\OAuthExtensionBundle\Security\Utils\ConfigProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class ConfigProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidConfiguration() {


        $containerMock = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $containerMock->expects($this->once())
            ->method('getParameter')
            ->willReturn(false);

        $routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configProvider = new ConfigProvider($containerMock, $routerMock);
        $configProvider->getRoleConfig();

    }


    public function testConfiguration() {


        $containerMock = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $containerMock->expects($this->any())
            ->method('getParameter')
            ->willReturnCallback(function($name) {

                $prefix = "vss_oauth_extension.auth.role";
                switch ($name) {

                    case "$prefix.client_id":
                        return 37;
                    case "$prefix.client_secret":
                        return "secret";
                    case "$prefix.endpoint":
                        return "/token";
                    case "$prefix.logout_path":
                        return "logout_path";

                }

            });

        $routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routerMock->expects($this->once())
            ->method('generate')
            ->with('logout_path')
            ->willReturn('/logout');

        $configProvider = new ConfigProvider($containerMock, $routerMock);
        $config = $configProvider->getRoleConfig();

        $expected = [
            "client_id" => 37,
            "client_secret" => "secret",
            "endpoint" => "/token",
            "logout_path" => "/logout"
        ];

        $this->assertEquals($expected, $config);

    }

}