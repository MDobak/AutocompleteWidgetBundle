<?php

namespace Mdobak\AutocompleteWidgetBundle\Tests\DataProvider;

use Mdobak\AutocompleteWidgetBundle\DataProvider\AutocompleteItem;
use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderInterface;

/**
 * A lot of test relies on data returned by this data provider. Changes can break tests!
 *
 * Contains 1000 items. Each item has id between 0 and 999 and label starting with id divided by 10 and rounded down
 * with "label" suffix (e.g. item with id 226 will have "22label").
 *
 * Original items are stdClass with two properties: key and label.
 */
class DummyDataProvider implements DataProviderInterface
{
    /**
     * @var AutocompleteItem[]
     */
    private $data = [];

    public function __construct()
    {
        for ($id = 0; $id < 1000; $id++) {
            $this->data[] = new AutocompleteItem(
                $id,
                floor($id/10).'label',
                (object)['key' => $id, 'label' => floor($id/10).'label']
            );
        }
    }

    public function findItem($key)
    {
        $result = $this->findItems([$key]);

        if (0 === count($result)) {
            return null;
        }

        return reset($result);
    }

    public function findItems($keys)
    {
        $results = [];
        foreach ($keys as $key) {
            if (isset($this->data[$key])) {
                $results[] = $this->data[$key];
            }
        }

        return $results;
    }

    public function findItemsForAutocomplete($searchString)
    {
        $result = [];
        foreach ($this->data as $item) {
            if (substr($item->getLabel(), 0, strlen($searchString)) === $searchString) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public function wrapItem($item)
    {
        return new AutocompleteItem($item->key, $item->label, $item);
    }
}