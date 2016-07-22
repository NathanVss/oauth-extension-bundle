<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 04/06/16
 * Time: 12:38
 */

namespace Vss\OAuthExtensionBundle\Events;

use AppBundle\Entity\User;
use FOS\OAuthServerBundle\Model\AccessTokenInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenDeliveredEvent extends Event {

    const NAME = 'vss_oauth_extension.events.token_delivered';

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var AccessTokenInterface
     */
    protected $token;

    /**
     * @return UserInterface
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * @return AccessTokenInterface
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param AccessTokenInterface $token
     */
    public function setToken($token) {
        $this->token = $token;
    }

}