<?php

namespace Kamilmusial\NtfyPhpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ntfy_php');
        $root = $treeBuilder->getRootNode();

        /** @phpstan-ignore-next-line */
        $root
            ->children()
                ->arrayNode('clients')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('uri')->end()
                            ->arrayNode('auth')
                                ->children()
                                    ->enumNode('type')
                                        ->values(['basic', 'token'])
                                    ->end()
                                    ->scalarNode('username')->end()
                                    ->scalarNode('password')->end()
                                    ->scalarNode('token')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}