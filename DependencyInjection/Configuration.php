<?php

namespace Vss\OAuthExtensionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vss_oauth_extension');

        $rootNode
            ->children()
                ->arrayNode('providers_options')
                    ->children()
                        ->scalarNode('fosub')->defaultFalse()->end()
                        ->scalarNode('user_manager')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('auth')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('client_id')->isRequired()->end()
                            ->scalarNode('client_secret')->isRequired()->end()
                            ->scalarNode('endpoint')->isRequired()->end()
                            ->scalarNode('logout_path')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->buildProvidersNode($rootNode);

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }

    public function buildProvidersNode(ArrayNodeDefinition $node) {

        $node
            ->children()
                ->arrayNode('providers')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')->isRequired()->end()
                            ->scalarNode('client_id')->isRequired()->end()
                            ->scalarNode('client_secret')->isRequired()->end()
                            ->scalarNode('redirect_uri')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

    }

}
