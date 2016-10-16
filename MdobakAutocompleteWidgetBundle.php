<?php

namespace Mdobak\AutocompleteWidgetBundle;

use Mdobak\AutocompleteWidgetBundle\DependencyInjection\CompilerPass\DataProviderCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MdobakAutocompleteWidgetBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DataProviderCompilerPass());
    }
}
