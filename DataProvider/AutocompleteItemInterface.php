<?php

namespace Mdobak\AutocompleteWidgetBundle\DataProvider;

interface AutocompleteItemInterface
{
    /**
     * ItemInterface constructor.
     *
     * @param int|string $key
     * @param string     $label
     * @param mixed      $originalItem
     */
    public function __construct($key, $label, $originalItem);

    /**
     * @return int|string
     */
    public function getKey();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return mixed
     */
    public function getOriginalItem();
}
