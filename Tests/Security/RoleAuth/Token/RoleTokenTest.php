<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 17:25
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\RoleAuth\Token;

use Vss\OAuthExtensionBundle\Security\RoleAuth\Token\RoleToken;

class RoleTokenTest extends \PHPUnit_Framework_TestCase
{

    public function test() {

        $token = new RoleToken('admin', 'pass', 'key', ['ROLE_ADMIN']);
        $token->setExpiresIn(60*60);
        $token->setAccessToken('access_token');
        $token->setRefreshToken('refresh_token');
        $token->setRoleRequired('ROLE_ADMIN');

        $this->assertEquals(60*60, $token->getExpiresIn());
        $this->assertEquals('access_token', $token->getAccessToken());
        $this->assertEquals('refresh_token', $token->getRefreshToken());
        $this->assertEquals('ROLE_ADMIN', $token->getRoleRequired());
        $this->assertTrue(!$token->isExpired());

        $token->getCreatedAt();

        $serialized = $token->serialize();

        $token2 = new RoleToken('user', 'pass', 'key', ['ROLE_USER']);
        $this->assertEquals(null, $token2->getAccessToken());
        $token2->unserialize($serialized);
        $this->assertEquals('access_token', $token2->getAccessToken());


    }

}