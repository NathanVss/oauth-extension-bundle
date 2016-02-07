<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 15:32
 */

namespace Vss\OAuthExtensionBundle\Security\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class ConfigProvider
 * @package Vss\OAuthExtensionBundle\Security\Utils
 */
class ConfigProvider
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Router
     */
    private $router;

    /**
     * ConfigProvider constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, Router $router) {
        $this->container = $container;
        $this->router = $router;
    }

    /**
     * @return array
     * @throw InvalidConfigurationException
     */
    public function getRoleConfig() {
        $prefix = "vss_oauth_extension.auth.role";
        if (!$this->container->getParameter("$prefix.client_id")) {
            throw new InvalidConfigurationException("No $prefix node configured.");
        }

        return [
            'client_id' => $this->container->getParameter("$prefix.client_id"),
            'client_secret' => $this->container->getParameter("$prefix.client_secret"),
            'endpoint' => $this->container->getParameter("$prefix.endpoint"),
            'logout_path' => $this->router->generate($this->container->getParameter("$prefix.logout_path"))
        ];
    }
}