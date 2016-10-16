<?php

namespace Mdobak\AutocompleteWidgetBundle\DataProvider;

class AutocompleteItem implements AutocompleteItemInterface
{
    /**
     * @var int|string
     */
    protected $key;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var mixed
     */
    protected $originalItem;

    /**
     * {@inheritDoc}
     */
    public function __construct($key, $label, $originalItem)
    {
        $this->key          = $key;
        $this->label        = $label;
        $this->originalItem = $originalItem;
    }

    /**
     * {@inheritDoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalItem()
    {
        return $this->originalItem;
    }
}
