<?php

namespace Mdobak\AutocompleteWidgetBundle\Routing;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class ApiPathFinder
 *
 * @author Michał Dobaczewski <mdobak@gmail.com>
 */
class ApiPathFinder
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * ApiPathFinder constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function findApiPathForDataProvider($providerServiceId)
    {
        foreach ($this->router->getRouteCollection()->all() as $name => $route) {
            if ('Mdobak\AutocompleteWidgetBundle\Controller\ApiController::showAction' !== $route->getDefault('_controller')) {
                continue;
            }

            $dataProvider = $route->getDefault('autocomplete_data_provider');

            if ($dataProvider !== $providerServiceId) {
                continue;
            }

            return $this->router->generate($name);
        }
    }
}
