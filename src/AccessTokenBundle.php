<?php

namespace AccessToken;

use AccessToken\Controller\TestController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class AccessTokenBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('jwt')
                    ->children()
                        ->scalarNode('user_entity')->end()
                        ->scalarNode('secret')->end()
                        ->scalarNode('issuer')->end()
                        ->scalarNode('algorithm')->end()
                        ->scalarNode('key')->end()
                        ->scalarNode('expire_at')->end()
                    ->end()
                ->end() // jwt
            ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('./../config/services.yaml');

        $container->parameters()->set('jwt.user_entity', $config['jwt']['user_entity']);
        $container->parameters()->set('jwt.secret', $config['jwt']['secret']);
        $container->parameters()->set('jwt.issuer', $config['jwt']['issuer']);
        $container->parameters()->set('jwt.algorithm', $config['jwt']['algorithm']);
        $container->parameters()->set('jwt.key', $config['jwt']['key']);
        $container->parameters()->set('jwt.expire_at', $config['jwt']['expire_at']);
        $container->services()->load(
            'AccessToken\Controller\\',
            './Controller/'
        )
            ->tag('controller.service_arguments');
        $loader = new YamlFileLoader(
            $builder,
            new FileLocator(__DIR__.'/../config')
        );
        $loader->load('services.yaml');
        /**
        $container->services()
            ->set(TestController::class)
            ->tag('controller.service_arguments')
            ->call('setContainer', [service('service_container')]);
         */
    }


}