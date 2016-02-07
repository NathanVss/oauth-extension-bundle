<?php
namespace Vss\OAuthExtensionBundle\Security\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Vss\OAuthExtensionBundle\Security\RoleAuth\Token\RoleToken;
use Vss\OAuthExtensionBundle\Security\Utils\ConfigProvider;

/**
 * Class ExpiredListener
 * @package Vss\OAuthExtensionBundle\Security\Listener
 */
class ExpiredListener
{

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * ExpiredListener constructor.
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage, ConfigProvider $configProvider) {
        $this->tokenStorage = $tokenStorage;
        $this->configProvider = $configProvider;
    }

    /**
     * Logout the user if the token is expired
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event) {

        $token = $this->tokenStorage->getToken();
        if (!($token instanceof RoleToken)) {
            return;
        }

        if (!$token->isExpired()) {
            return;
        }

        $config = $this->configProvider->getRoleConfig();
        $response = new RedirectResponse($config['logout_path']);
        $event->setResponse($response);
    }

}