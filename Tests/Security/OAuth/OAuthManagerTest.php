<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 18:10
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\OAuth;

use Vss\OAuthExtensionBundle\Security\OAuth\OAuthManager;
use Vss\OAuthExtensionBundle\Tests\Fake\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuthManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testProviderNotExists() {
        $this->setExpectedException('Vss\OAuthExtensionBundle\Security\OAuth\Exception\ProviderNotExistsException');

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $em = $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects($this->once())
            ->method('has')
            ->willReturn(false);

        $manager = new OAuthManager($container, $em);

        $manager->getProvider('facebook');
    }

    public function testProvider() {

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $em = $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects($this->once())
            ->method('has')
            ->willReturn(true);
        $container->expects($this->once())
            ->method('get')
            ->willReturn("OAuth2Provider");

        $manager = new OAuthManager($container, $em);

        $manager->getProvider('facebook');
    }

}