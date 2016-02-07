<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 28/01/16
 * Time: 13:53
 */

namespace Vss\OAuthExtensionBundle\Security\Utils;

use Buzz\Browser;

/**
 * Class BrowserManager
 * @package Vss\OAuthExtensionBundle\Security\Utils
 */
class BrowserManager
{

    /**
     * @return Browser
     */
    public function getBrowser() {
        return new Browser();
    }

}