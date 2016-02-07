<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 18:10
 */

namespace Vss\OAuthExtensionBundle\Tests\Security\Utils;

use Vss\OAuthExtensionBundle\Security\Utils\BrowserManager;
use Buzz\Browser;

class BrowserManagerTest extends \PHPUnit_Framework_TestCase
{

    public function test() {

        $browserManager = new BrowserManager();

        $this->assertTrue($browserManager->getBrowser() instanceof Browser);

    }

}