<?php

namespace Doppy\UtilBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('doppy_util');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('temp_file')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('path')->defaultFalse()->end()
                        ->booleanNode('cleanup_on_terminate')->defaultTrue()->end()
                    ->end()
                ->end()
                ->booleanNode('nullstopwatch')->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
