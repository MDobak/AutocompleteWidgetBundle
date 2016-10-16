<?php

namespace Mdobak\AutocompleteWidgetBundle\DataProvider;

interface DataProviderInterface
{
    /**
     * @param int|string $key
     *
     * @return AutocompleteItemInterface
     */
    public function findItem($key);

    /**
     * @param int[]|string[] $keys
     *
     * @return AutocompleteItemInterface[]
     */
    public function findItems($keys);

    /**
     * @param string $searchString
     *
     * @return AutocompleteItemInterface[]
     */
    public function findItemsForAutocomplete($searchString);

    /**
     * @param mixed $item
     *
     * @return AutocompleteItem
     */
    public function wrapItem($item);
}
