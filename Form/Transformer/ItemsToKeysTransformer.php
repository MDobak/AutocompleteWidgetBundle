<?php

namespace Mdobak\AutocompleteWidgetBundle\Form\Transformer;

use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class ItemsToKeysTransformer
 *
 * @author Michał Dobaczewski <mdobak@gmail.com>
 */
class ItemsToKeysTransformer implements DataTransformerInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * ItemsToKeysTransformer constructor.
     *
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param mixed $items
     *
     * @return array
     */
    public function transform($items)
    {
        if (0 === count($items) || null === $items) {
            return [];
        }

        $result = [];
        foreach ($items as $item) {
            $result[] = $this->dataProvider->wrapItem($item)->getKey();
        }

        return $result;
    }

    /**
     * @param mixed $keys
     *
     * @return array
     */
    public function reverseTransform($keys)
    {
        if (0 === count($keys) || null === $keys) {
            return [];
        }

        $items = $this->dataProvider->findItems($keys);

        if (count($items) !== count($keys)) {
            throw new TransformationFailedException('Could not find all matching items for the given values');
        }

        $result = [];
        foreach ($items as $item) {
            $result[] = $item->getOriginalItem();
        }

        return $result;
    }
}
