<?php

namespace Goleadsit\AdminCrudBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class GoleadsitAdminCrudExtension extends Extension implements PrependExtensionInterface {

    public function load(array $configs, ContainerBuilder $container) {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('goleadsit_admin_crud.config_manager');
        $definition->setArgument(0, $config);
    }

    public function prepend(ContainerBuilder $container) {
        $knpPaginatorConfig = [
            'template' => [
                'pagination' => '@KnpPaginator/Pagination/twitter_bootstrap_v3_pagination.html.twig',
                'sortable'   => '@KnpPaginator/Pagination/font_awesome_sortable_link.html.twig'
            ]
        ];
        $container->prependExtensionConfig('knp_paginator', $knpPaginatorConfig);
    }

    public function getAlias() {
        return 'goleadsit_admin_crud';
    }

}
