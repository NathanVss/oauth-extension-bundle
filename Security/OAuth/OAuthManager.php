<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 11/02/16
 * Time: 18:02
 */

namespace Vss\OAuthExtensionBundle\Security\OAuth;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;
use Vss\OAuthExtensionBundle\Security\OAuth\Exception\ProviderNotExistsException;
use Vss\OAuthExtensionBundle\Providers\OAuth2ProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class OAuthManager
 * @package Vss\OAuthExtensionBundle\Security\OAuth
 */
class OAuthManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    public function __construct(ContainerInterface $container, ObjectManager $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $name
     * @return OAuth2ProviderInterface
     * @throws ProviderNotExistsException
     */
    public function getProvider($name) {
        $service = "vss_oauth_extension.providers.$name";
        if (!$this->container->has($service)) {
            throw new ProviderNotExistsException(sprintf("The provider %s does not exists.", $name));
        }

        return $this->container->get($service);
    }



}