<?php

namespace Mdobak\AutocompleteWidgetBundle\Tests\Functional\Fixtures;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class WebServer
{
    /**
     * @var KernelProvider
     */
    protected static $kernelProvider;

    public static function start()
    {
        $application = new Application(self::getKernel());
        $application->setAutoExit(false);

        $input = new ArrayInput(
            [
                'command'   => 'server:start',
                '--docroot' => self::getDocRoot()
            ]
        );

        $output = new BufferedOutput();
        $application->run($input, $output);
    }

    public static function stop()
    {
        $application = new Application(self::getKernel());
        $application->setAutoExit(false);

        $input = new ArrayInput(
            [
                'command' => 'server:stop'
            ]
        );

        $output = new BufferedOutput();
        $application->run($input, $output);
    }

    /**
     * @return AppKernel
     */
    public static function getKernel()
    {
        if (null === self::$kernelProvider) {
            self::$kernelProvider = new KernelProvider();
        }

        return self::$kernelProvider->getKernel();
    }

    /**
     * @return string
     */
    public static function getDocRoot()
    {
        return __DIR__.'/public';
    }
}