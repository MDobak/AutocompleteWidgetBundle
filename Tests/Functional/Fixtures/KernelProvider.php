<?php

namespace Mdobak\AutocompleteWidgetBundle\Tests\Functional\Fixtures;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Mdobak\AutocompleteWidgetBundle\Tests\Functional\Fixtures\Entity\DummyEntity;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class KernelProvider
{
    /**
     * @var AppKernel
     */
    private static $kernel;

    private static $instancesCounter;

    public function __construct()
    {
        self::$instancesCounter++;
        $this->getKernel();
    }

    public function __destruct()
    {
        self::$instancesCounter--;

        if (0 === self::$instancesCounter) {
            $this->destroyKernel();
        }
    }

    public function getKernel()
    {
        if (!self::$kernel) {
            self::setUpKernel();
            self::setUpDoctrine();
            self::setSampleData();
        }

        return self::$kernel;
    }

    public function get($id)
    {
        return self::$kernel->getContainer()->get($id);
    }

    private function destroyKernel()
    {
        self::$kernel->shutdown();
        self::$kernel = null;
    }

    private function setUpKernel()
    {
        require_once __DIR__ . '/AppKernel.php';

        $kernel = new AppKernel('test', true);
        $kernel->boot();

        self::$kernel = $kernel;
    }

    private function setUpDoctrine()
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:update',
            '--force' => true,
        ));

        $output = new NullOutput();
        $application->run($input, $output);
    }

    private function setSampleData()
    {
        // A lot of test relies on this data. Changes can break tests!

        /** @var Registry $doctrine */
        $doctrine = self::$kernel->getContainer()->get('doctrine');

        for ($id = 1; $id <= 1000; ++$id) {
            $label = floor($id/10).'label';

            $item = new DummyEntity();
            $item->setId($id);
            $item->setName($label);

            $doctrine->getManager()->persist($item);
        }

        $doctrine->getManager()->flush();
    }
}