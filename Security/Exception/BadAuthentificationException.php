<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 28/01/16
 * Time: 13:29
 */

namespace Vss\OAuthExtensionBundle\Security\Exception;

class BadAuthentificationException extends \Exception
{

    private $oauthErrorName;

    private $oauthErrorDescription;

    /**
     * @return mixed
     */
    public function getOAuthErrorName()
    {
        return $this->oauthErrorName;
    }

    /**
     * @param mixed $oauthErrorName
     */
    public function setOAuthErrorName($oauthErrorName)
    {
        $this->oauthErrorName = $oauthErrorName;
    }

    /**
     * @return mixed
     */
    public function getOAuthErrorDescription()
    {
        return $this->oauthErrorDescription;
    }

    /**
     * @param mixed $oauthErrorDescription
     */
    public function setOAuthErrorDescription($oauthErrorDescription)
    {
        $this->oauthErrorDescription = $oauthErrorDescription;
    }

}