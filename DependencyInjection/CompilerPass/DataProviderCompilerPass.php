<?php

namespace Mdobak\AutocompleteWidgetBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class DataProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('mdobak_autocomplete_widget.data_provider_collection')) {
            return;
        }

        $definition = $container->findDefinition(
            'mdobak_autocomplete_widget.data_provider_collection'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'mdobak_autocomplete_widget.data_provider'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'add',
                array($id, new Reference($id))
            );
        }
    }
}