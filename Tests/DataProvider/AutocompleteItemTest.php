<?php

namespace Tests\Mdobak\AutocompleteWidgetBundle;

use Mdobak\AutocompleteWidgetBundle\DataProvider\AutocompleteItem;

class AutocompleteItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var mixed
     */
    private $originalItem;

    /**
     * @var AutocompleteItem
     */
    private $item;

    public function setUp()
    {
        $this->originalItem = (object)['key' => '#123', 'label' => 'Item123'];
        $this->item = new AutocompleteItem($this->originalItem->key, $this->originalItem->label, $this->originalItem);
    }

    public function testGetKey()
    {
        $this->assertSame($this->originalItem->key, $this->item->getKey());
    }

    public function testGetGetLabel()
    {
        $this->assertSame($this->originalItem->label, $this->item->getLabel());
    }

    public function testGetOriginalItem()
    {
        $this->assertSame(
            spl_object_hash($this->originalItem),
            spl_object_hash($this->item->getOriginalItem()),
            'Instance of object returned by AutocompleteItem::getOriginalItem is not same as original.'
        );
    }
}