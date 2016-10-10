<?php

namespace Mdobak\AutocompleteWidgetBundle\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Mdobak\AutocompleteWidgetBundle\DataProvider\Doctrine\ORM\DoctrineORMDataProvider;
use Mdobak\AutocompleteWidgetBundle\Tests\Functional\Fixtures\KernelProvider;

class DoctrineORMDataProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoctrineORMDataProvider
     */
    private $doctrineORMDataProvider;

    /**
     * @var KernelProvider
     */
    private $kernel;

    protected function setUp()
    {
        $this->kernel = new KernelProvider();

        $this->doctrineORMDataProvider = new DoctrineORMDataProvider(
            $this->kernel->get('doctrine')->getManager(),
            'Test:DummyEntity',
            'name'
        );
    }

    public function testFindItem()
    {
        /** @var EntityManager $em */
        $em = $this->kernel->get('doctrine')->getManager();

        $itemFromDataProvider = $this->doctrineORMDataProvider->findItem(10);
        $itemFromDatabase     = $em->find('Test:DummyEntity', 10);

        $this->assertInstanceOf('Mdobak\AutocompleteWidgetBundle\DataProvider\AutocompleteItem', $itemFromDataProvider);

        $this->assertSame($itemFromDatabase->getId(), $itemFromDataProvider->getKey());
        $this->assertSame($itemFromDatabase->getName(), $itemFromDataProvider->getLabel());
        $this->assertEquals(spl_object_hash($itemFromDataProvider->getOriginalItem()), spl_object_hash($itemFromDatabase));
    }

    public function testFindItems()
    {
        /** @var EntityManager $em */
        $em = $this->kernel->get('doctrine')->getManager();

        $itemsFromDataProvider = $this->doctrineORMDataProvider->findItems([1, 5, 10]);
        $itemsFromDatabase     = [
            $em->find('Test:DummyEntity', 1),
            $em->find('Test:DummyEntity', 5),
            $em->find('Test:DummyEntity', 10)
        ];

        $this->assertSame(3, count($itemsFromDataProvider));

        foreach ($itemsFromDataProvider as $n => $item) {
            $this->assertInstanceOf('Mdobak\AutocompleteWidgetBundle\DataProvider\AutocompleteItem', $item);

            $this->assertSame($itemsFromDatabase[$n]->getId(), $itemsFromDataProvider[$n]->getKey());
            $this->assertSame($itemsFromDatabase[$n]->getName(), $itemsFromDataProvider[$n]->getLabel());
            $this->assertEquals(spl_object_hash($itemsFromDataProvider[$n]->getOriginalItem()), spl_object_hash($itemsFromDatabase[$n]));
        }
    }

    public function testFindItemsForAutocomplete()
    {
        $itemsFromDataProvider = $this->doctrineORMDataProvider->findItemsForAutocomplete('0lab');

        $this->assertSame(9, count($itemsFromDataProvider));

        foreach ($itemsFromDataProvider as $item) {
            $this->assertInstanceOf('Mdobak\AutocompleteWidgetBundle\DataProvider\AutocompleteItem', $item);
            $this->assertSame('0lab', substr($item->getLabel(), 0, 4));
        }
    }
}