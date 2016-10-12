<?php

namespace Tests\Mdobak\AutocompleteWidgetBundle\Tests\Form\Type;

use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderCollection;
use Mdobak\AutocompleteWidgetBundle\Form\Type\AutocompleteCoreFormType;
use Mdobak\AutocompleteWidgetBundle\Routing\ApiPathFinder;
use Mdobak\AutocompleteWidgetBundle\Tests\DataProvider\DummyDataProvider;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class AutocompleteCoreFormTypeTest extends TypeTestCase
{
    /**
     * @var DataProviderCollection
     */
    private $dataProviderCollection;

    /**
     * @var ApiPathFinder
     */
    private $apiPathFinder;

    protected function setUp()
    {
        parent::setUp();

        // in this test case its impossible to test validating data!
        $validator = $this->getMockBuilder('\Symfony\Component\Validator\Validator\ValidatorInterface')->getMock();
        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new FormTypeValidatorExtension($validator))
            ->getFormFactory()
        ;
    }

    protected function getExtensions()
    {
        /** @var RouterInterface $router */
        $router = $this->getMockBuilder('Symfony\\Component\\Routing\\RouterInterface')->getMock();

        $routeCollection = new RouteCollection();
        $routeCollection->add(
            'test',
            new Route(
                '/test',
                ['_controller' => 'MdobakAutocompleteWidgetBundle:Api:show'],
                [],
                ['autocomplete_data_provider' => 'test']
            )
        );

        $router->method('getRouteCollection')->willReturn($routeCollection);

        $this->dataProviderCollection = new DataProviderCollection();
        $this->apiPathFinder = new ApiPathFinder($router);

        $this->dataProviderCollection->add('dummy_data_provider', new DummyDataProvider());

        $formType = new FormType();
        $autocompleteCoreType = new AutocompleteCoreFormType($this->dataProviderCollection, $this->apiPathFinder);

        return array(
            // register the type instances with the PreloadedExtension
            new PreloadedExtension([
                'form'                           => $formType,
                $autocompleteCoreType->getName() => $autocompleteCoreType
            ], []),
        );
    }

    public function testSubmitted()
    {
        $form = $this->factory->create($this->getFormName(), null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => false
        ]);

        $form->submit(10);

        $this->assertTrue($form->isSubmitted());
    }

    public function testNotSubmitted()
    {
        $form = $this->factory->create($this->getFormName(), null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => false
        ]);

        $this->assertFalse($form->isSubmitted());
    }

    public function testSubmitSingleValidItem()
    {
        $form = $this->factory->create($this->getFormName(), null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => false
        ]);

        $form->submit(10);

        $this->assertEquals(
            $this->dataProviderCollection->get('dummy_data_provider')->findItem(10)->getOriginalItem(),
            $form->getData()
        );
    }

    public function testSubmitMultipleValidItems()
    {
        $form = $this->factory->create($this->getFormName(), null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => true
        ]);

        $form->submit([1, 5, 10]);

        $this->assertSame(3, count($form->getData()));

        $items = $this->dataProviderCollection->get('dummy_data_provider')->findItems([1, 5, 10]);

        $this->assertEquals(
            [
                $items[0]->getOriginalItem(),
                $items[1]->getOriginalItem(),
                $items[2]->getOriginalItem()
            ],
            $form->getData()
        );
    }

    public function testSetSingleValidItem()
    {
        $form = $this->factory->create($this->getFormName(), null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => false
        ]);

        $form->setData($this->dataProviderCollection->get('dummy_data_provider')->findItem(10)->getOriginalItem());

        $this->assertEquals(
            $this->dataProviderCollection->get('dummy_data_provider')->findItem(10)->getOriginalItem(),
            $form->getData()
        );
    }

    public function testSetMultipleValidItems()
    {
        $form = $this->factory->create($this->getFormName(), null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => true
        ]);

        $form->setData([
            $this->dataProviderCollection->get('dummy_data_provider')->findItem(1)->getOriginalItem(),
            $this->dataProviderCollection->get('dummy_data_provider')->findItem(5)->getOriginalItem(),
            $this->dataProviderCollection->get('dummy_data_provider')->findItem(10)->getOriginalItem()
        ]);

        $this->assertSame(3, count($form->getData()));

        $items = $this->dataProviderCollection->get('dummy_data_provider')->findItems([1, 5, 10]);

        $this->assertEquals(
            [
                $items[0]->getOriginalItem(),
                $items[1]->getOriginalItem(),
                $items[2]->getOriginalItem()
            ],
            $form->getData()
        );
    }

    private function getFormName()
    {
        if (Kernel::MAJOR_VERSION == 2 && Kernel::MINOR_VERSION <= 7) {
            return 'mdobak_autocomplete_core';
        }
        
        return AutocompleteCoreFormType::class;
    }
}