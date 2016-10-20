<?php

namespace Mdobak\AutocompleteWidgetBundle\Tests\Functional\Fixtures;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Mdobak\AutocompleteWidgetBundle\MdobakAutocompleteWidgetBundle(),
            new \Mdobak\AutocompleteWidgetBundle\Tests\Functional\Fixtures\TestBundle\TestBundle()
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/MdobakAutocompleteWidgetBundleTests/cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir().'/MdobakAutocompleteWidgetBundleTests/log';
    }

    public function shutdown()
    {
        parent::shutdown();

        $dirPath = sys_get_temp_dir().'/MdobakAutocompleteWidgetBundleTests';

        if (!file_exists($dirPath)) {
            return;
        }

        $it    = new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dirPath);
    }
}