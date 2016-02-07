<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 17:33
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\Exception;

use Vss\OAuthExtensionBundle\Security\Exception\BadAuthentificationException;

class ExceptionsTest extends \PHPUnit_Framework_TestCase
{

    public function testBadAuthentificationException() {

        $exception = new BadAuthentificationException();
        $exception->setOAuthErrorDescription('Invalid credentials');
        $exception->setOAuthErrorName('bad_credentials');

        $this->assertEquals('Invalid credentials', $exception->getOAuthErrorDescription());
        $this->assertEquals('bad_credentials', $exception->getOAuthErrorName());
    }

}