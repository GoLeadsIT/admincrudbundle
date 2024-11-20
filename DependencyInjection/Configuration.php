<?php

namespace Goleadsit\AdminCrudBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('goleadsit_admin_crud');

        $treeBuilder->getRootNode()
            ->useAttributeAsKey('entityName')
            ->info('Nombre de la entity')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('route')->info('Prefijo para la ruta')->end()
                    ->scalarNode('form')->info('Form')->end()
                    ->arrayNode('actions')
                        ->info('Habilitar o deshabilitar acciones')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('index')->defaultTrue()->end()
                            ->booleanNode('new')->defaultTrue()->end()
                            ->booleanNode('edit')->defaultTrue()->end()
                            ->booleanNode('show')->defaultTrue()->end()
                            ->booleanNode('delete')->defaultTrue()->end()
                        ->end()
                    ->end()
                    ->arrayNode('paths')
                    ->info('Nombre que se asociará a la ruta')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('index')->defaultNull()->end()
                            ->scalarNode('new')->defaultNull()->end()
                            ->scalarNode('edit')->defaultNull()->end()
                            ->scalarNode('show')->defaultNull()->end()
                            ->scalarNode('delete')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

}
