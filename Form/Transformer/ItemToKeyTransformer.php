<?php

namespace Mdobak\AutocompleteWidgetBundle\Form\Transformer;

use Mdobak\AutocompleteWidgetBundle\DataProvider\AutocompleteItemInterface;
use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class ItemToKeyTransformer
 *
 * @author Michał Dobaczewski <mdobak@gmail.com>
 */
class ItemToKeyTransformer implements DataTransformerInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * ItemToKeyTransformer constructor.
     *
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param mixed $item
     *
     * @return int|null|string
     */
    public function transform($item)
    {
        if (null === $item) {
            return null;
        }

        return $this->dataProvider->wrapItem($item)->getKey();
    }

    /**
     * @param mixed $key
     *
     * @return mixed|null
     */
    public function reverseTransform($key)
    {
        if (null === $key) {
            return null;
        }

        $item = $this->dataProvider->findItem($key);

        if (! ($item instanceof AutocompleteItemInterface)) {
            throw new TransformationFailedException(sprintf('The item "%s" does not exist', $key));
        }

        return $item->getOriginalItem();
    }
}
