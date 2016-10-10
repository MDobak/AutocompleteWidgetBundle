<?php

namespace Mdobak\AutocompleteWidgetBundle\Tests\Routing;

use Mdobak\AutocompleteWidgetBundle\Routing\ApiPathFinder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class ApiPathFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApiPathFinder
     */
    protected $apiPathFinder;

    public function setUp()
    {
        $router = $this->getMockBuilder('Symfony\\Component\\Routing\\RouterInterface')->getMock();

        $routeCollection = new RouteCollection();
        $routeCollection->add(
            'test',
            new Route(
                '/test',
                [
                    '_controller' => 'Mdobak\AutocompleteWidgetBundle\Controller\ApiController::showAction',
                    'autocomplete_data_provider' => 'test_data_provider'
                ]
            )
        );

        $router->method('getRouteCollection')->willReturn($routeCollection);
        $router->method('generate')->will($this->returnCallback(function($route) {
            return '/test';
        }));
        $router->method('generate')->with($this->equalTo('test'));

        $this->apiPathFinder = new ApiPathFinder($router);
    }

    public function testFindApiPathForDataProvider()
    {
        $path = $this->apiPathFinder->findApiPathForDataProvider('test_data_provider');

        $this->assertEquals('/test', $path);
    }
}