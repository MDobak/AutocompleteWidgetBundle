<?php

namespace Mdobak\AutocompleteWidgetBundle\Routing;

/**
 * Interface ApiPathFinderInterface
 *
 * @author Michał Dobaczewski <mdobak@gmail.com>
 */
interface ApiPathFinderInterface
{
    /**
     * @param $providerServiceId
     *
     * @return string
     */
    public function findApiPathForDataProvider($providerServiceId);
}