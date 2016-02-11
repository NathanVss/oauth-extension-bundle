<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 23:16
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\OAuth;

use FOS\UserBundle\Util\UserManipulator;
use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use Vss\OAuthExtensionBundle\Tests\Fake\FOSUBUser;
use Vss\OAuthExtensionBundle\Security\OAuth\OAuthFOSUBManager;

class OAuthFOSUBManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdateAccessToken() {
        $user = $this->getMockBuilder(FOSUBUser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $user->expects($this->once())
            ->method('setFacebookAccessToken');

        $userManipulator = $this->getMockBuilder(UserManipulator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fosUserManager = $this->getMockBuilder(FOSUserManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $manager = new OAuthFOSUBManager($userManipulator, $fosUserManager);

        $data = [
            'id' => 37,
            'data' => [
                'name' => 'NathanVss'
            ]
        ];
        $manager->updateAccessToken($user, 'facebook', 'token');
    }


    public function testHandleOAuthResponseUserExists() {

        $userManipulator = $this->getMockBuilder(UserManipulator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fosUserManager = $this->getMockBuilder(FOSUserManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fosUserManager->expects($this->once())
            ->method('findUserBy')
            ->willReturn('user');

        $manager = new OAuthFOSUBManager($userManipulator, $fosUserManager);

        $data = [
            'id' => 37,
            'data' => [
                'name' => 'NathanVss'
            ]
        ];
        $output = $manager->getUserFromOAuthResponse('facebook', $data);

        $this->assertEquals('user', $output);
    }

    public function testHandleOAuthResponseUserCreated() {


        $user = $this->getMockBuilder(FOSUBUser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $user->expects($this->once())
            ->method('setFacebookId')
            ->willReturn(null);

        $userManipulator = $this->getMockBuilder(UserManipulator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userManipulator->expects($this->once())
            ->method('create')
            ->willReturn($user);

        $fosUserManager = $this->getMockBuilder(FOSUserManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fosUserManager->expects($this->once())
            ->method('findUserBy')
            ->willReturn(null);
        $fosUserManager->expects($this->once())
            ->method('updateUser')
            ->willReturnCallback(function() {
               return null;
            });

        $manager = new OAuthFOSUBManager($userManipulator, $fosUserManager);

        $data = [
            'id' => 37,
            'data' => [
                'name' => 'NathanVss'
            ]
        ];
        $output = $manager->getUserFromOAuthResponse('facebook', $data);

        $this->assertTrue($output instanceof FOSUBUser);
    }

}