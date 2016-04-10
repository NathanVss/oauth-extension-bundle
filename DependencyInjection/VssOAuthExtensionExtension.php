<?php

namespace Vss\OAuthExtensionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class VssOAuthExtensionExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // client side authentication
        if (isset($config['auth'])) {
            $prefix = "vss_oauth_extension.auth";
            foreach ($config['auth'] as $name => $auth) {
                $prefix .= ".$name";
                foreach ($auth as $key => $value) {
                    $container->setParameter($prefix . ".$key", $value);
                }
            }
        }

        // server side oauth2 provider
        if (isset($config['providers'])) {
            foreach ($config['providers'] as $name => $options) {
                $this->setupProviderService($container, $name, $options);
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param ContainerBuilder $container
     * @param string $name
     * @param string $options
     */
    private function setupProviderService(ContainerBuilder $container, $name, $options) {

        $type = $options['type'];
        unset($options['type']);

        $container->setParameter("vss_oauth_extension.providers.$name.client_id", $options['client_id']);

        $definition = new DefinitionDecorator('vss_oauth_extension.providers.generic.oauth2');
        $definition->setClass("%vss_oauth_extension.providers.$type.class%");
        $container->setDefinition('vss_oauth_extension.providers.'.$name, $definition);
        $definition
            ->replaceArgument(1, $options)
            ->replaceArgument(2, $name)
        ;

    }



    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'vss_oauth_extension';
    }
}
