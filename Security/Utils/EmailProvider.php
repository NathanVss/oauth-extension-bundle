<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 10/04/16
 * Time: 15:51
 */

namespace Vss\OAuthExtensionBundle\Security\Utils;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface EmailProvider
 * @package Vss\OAuthExtensionBundle\Security\Utils
 */
interface EmailProvider {

    /**
     * @param string $email
     * @return UserInterface
     */
    public function loadByEmail($email);

}