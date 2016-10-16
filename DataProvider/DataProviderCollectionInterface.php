<?php

namespace Mdobak\AutocompleteWidgetBundle\DataProvider;


interface DataProviderCollectionInterface extends \IteratorAggregate, \Countable
{

    /**
     * Gets the current DataProviderInterfaceCollection as an Iterator that includes all dataProviders.
     *
     * It implements \IteratorAggregate.
     *
     * @see all()
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over dataProviders
     */
    public function getIterator();

    /**
     * Gets the number of DataProviderInterfaces in this collection.
     *
     * @return int The number of dataProviders
     */
    public function count();

    /**
     * Adds a route.
     *
     * @param string $name  The route name
     * @param DataProviderInterface  $dataProvider A DataProviderInterface instance
     */
    public function add($name, DataProviderInterface $dataProvider);

    /**
     * Returns all dataProviders in this collection.
     *
     * @return DataProviderInterface[] An array of dataProviders
     */
    public function all();

    /**
     * Gets a route by name.
     *
     * @param string $name The route name
     *
     * @return DataProviderInterface|null A DataProviderInterface instance or null when not found
     */
    public function get($name);

    /**
     * Removes a route or an array of dataProviders by name from the collection.
     *
     * @param string|array $name The route name or an array of route names
     */
    public function remove($name);

}
