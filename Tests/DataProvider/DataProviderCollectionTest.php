<?php

namespace Mdobak\AutocompleteWidgetBundle\Tests\DataProvider;

use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderCollection;

class DataProviderCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCount()
    {
        $collection = new DataProviderCollection();
        $collection->add('test1', new DummyDataProvider());
        $collection->add('test2', new DummyDataProvider());

        $this->assertSame(2, count($collection));
    }

    public function testOverride()
    {
        $collection = new DataProviderCollection();

        $dp1 = new DummyDataProvider();
        $dp2 = new DummyDataProvider();

        $collection->add('test', $dp1);
        $collection->add('test', $dp2);

        $this->assertSame(1, count($collection));
        $this->assertEquals(spl_object_hash($collection->get('test')), spl_object_hash($dp2));
    }

    public function testAddAndGet()
    {
        $collection = new DataProviderCollection();

        $provider1 = new DummyDataProvider();
        $provider2 = new DummyDataProvider();

        $collection->add('test1', $provider1);
        $collection->add('test2', $provider2);

        $this->assertEquals(spl_object_hash($provider1), spl_object_hash($collection->get('test1')));
        $this->assertEquals(spl_object_hash($provider2), spl_object_hash($collection->get('test2')));
    }

    public function testRemove()
    {
        $collection = new DataProviderCollection();
        $collection->add('test1', new DummyDataProvider());
        $collection->add('test2', new DummyDataProvider());

        $collection->remove('test2');

        $this->assertNull($collection->get('test2'));
    }
}
