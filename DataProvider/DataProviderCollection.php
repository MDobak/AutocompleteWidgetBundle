<?php

namespace Mdobak\AutocompleteWidgetBundle\DataProvider;

class DataProviderCollection implements DataProviderCollectionInterface
{
    /**
     * @var DataProviderInterface[]
     */
    protected $dataProviders = array();

    public function __clone()
    {
        foreach ($this->dataProviders as $name => $dataProvider) {
            $this->dataProviders[$name] = clone $dataProvider;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->dataProviders);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->dataProviders);
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, DataProviderInterface $dataProvider)
    {
        unset($this->dataProviders[$name]);

        $this->dataProviders[$name] = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->dataProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return isset($this->dataProviders[$name]) ? $this->dataProviders[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        foreach ((array)$name as $n) {
            unset($this->dataProviders[$n]);
        }
    }

}