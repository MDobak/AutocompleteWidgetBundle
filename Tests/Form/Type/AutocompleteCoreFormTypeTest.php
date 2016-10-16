<?php

namespace Tests\Mdobak\AutocompleteWidgetBundle\Tests\Form\Type;

use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderCollectionInterface;
use Mdobak\AutocompleteWidgetBundle\Form\Type\AutocompleteCoreFormType;
use Mdobak\AutocompleteWidgetBundle\Routing\ApiPathFinder;
use Mdobak\AutocompleteWidgetBundle\Tests\DataProvider\DummyDataProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutocompleteCoreFormTypeTest extends TypeTestCase
{
    public function testBuildForm()
    {
        $builder = $this->getMockBuilder(FormBuilder::class)->disableOriginalConstructor()->getMock();
        $dataProviderCollection = $this->getMockBuilder(DataProviderCollectionInterface::class)->getMock();
        $apiPathFinder = $this->getMockBuilder(ApiPathFinder::class)->disableOriginalConstructor()->getMock();

        $dataProviderCollection
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('test_data_provider'))
            ->willReturn(new DummyDataProvider())
        ;

        $type = new AutocompleteCoreFormType($dataProviderCollection, $apiPathFinder);

        $options = [
            'multiple'                => false,
            'data_provider'           => 'test_data_provider',
            'api_path'                => null,
            'item_translation_domain' => false,
        ];

        $type->buildForm($builder, $options);
    }

    /**
     * @dataProvider testViewDataProvider
     */
    public function testBuildView($expectedViewVariableValue, $viewVariableKey, $options)
    {
        $formView = $this->getMockBuilder(FormView::class)->disableOriginalConstructor()->getMock();
        $form = $this->getMockBuilder(FormInterface::class)->getMock();
        $dataProviderCollection = $this->getMockBuilder(DataProviderCollectionInterface::class)->getMock();
        $apiPathFinder = $this->getMockBuilder(ApiPathFinder::class)->disableOriginalConstructor()->getMock();

        $formView->vars['full_name'] = 'form';

        $dataProviderCollection
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('test_data_provider'))
            ->willReturn(new DummyDataProvider())
        ;

        $apiPathFinder
            ->expects($this->once())
            ->method('findApiPathForDataProvider')
            ->with($this->equalTo('test_data_provider'))
            ->willReturn('/test')
        ;

        if ($options['multiple']) {
            $form->method('getData')->willReturn([(object)['key' => 1, 'label' => 'label1']]);
        } else {
            $form->method('getData')->willReturn((object)['key' => 1, 'label' => 'label1']);
        }

        $type = new AutocompleteCoreFormType($dataProviderCollection, $apiPathFinder);

        $options = array_replace([
            'data_provider' => 'test_data_provider',
        ], $options);

        $type->buildView($formView, $form, $options);

        $this->assertEquals($expectedViewVariableValue, $formView->vars[$viewVariableKey]);
    }

    /**
     * @expectedException Symfony\Component\Form\Exception\InvalidConfigurationException
     */
    public function testBuildFormException()
    {
        $builder = $this->getMockBuilder(FormBuilder::class)->disableOriginalConstructor()->getMock();
        $dataProviderCollection = $this->getMockBuilder(DataProviderCollectionInterface::class)->getMock();
        $apiPathFinder = $this->getMockBuilder(ApiPathFinder::class)->disableOriginalConstructor()->getMock();

        $type = new AutocompleteCoreFormType($dataProviderCollection, $apiPathFinder);

        $options = [];
        $type->buildForm($builder, $options);
    }

    public function testSetDefaultOptions()
    {
        $dataProviderCollection = $this->getMockBuilder(DataProviderCollectionInterface::class)->getMock();
        $apiPathFinder = $this->getMockBuilder(ApiPathFinder::class)->disableOriginalConstructor()->getMock();
        $resolver = $this->getMockBuilder(OptionsResolver::class)->getMock();
        $resolver->expects($this->once())->method('setDefaults');

        $type = new AutocompleteCoreFormType($dataProviderCollection, $apiPathFinder);

        $type->configureOptions($resolver);
    }

    public function testGetBlockPrefix()
    {
        if (!method_exists(AbstractType::class, 'getBlockPrefix')) {
            $this->markTestSkipped();
        }

        $dataProviderCollection = $this->getMockBuilder(DataProviderCollectionInterface::class)->getMock();
        $apiPathFinder = $this->getMockBuilder(ApiPathFinder::class)->disableOriginalConstructor()->getMock();

        $type = new AutocompleteCoreFormType($dataProviderCollection, $apiPathFinder);

        $this->assertEquals('mdobak_autocomplete_core', $type->getBlockPrefix());
    }

    public function testGetName()
    {
        if (!method_exists(AbstractType::class, 'getName')) {
            $this->markTestSkipped();
        }

        $dataProviderCollection = $this->getMockBuilder(DataProviderCollectionInterface::class)->getMock();
        $apiPathFinder = $this->getMockBuilder(ApiPathFinder::class)->disableOriginalConstructor()->getMock();

        $type = new AutocompleteCoreFormType($dataProviderCollection, $apiPathFinder);

        $this->assertEquals('mdobak_autocomplete_core', $type->getName());
    }

    public function testViewDataProvider()
    {
        return [
            [
                '/test',
                'api_path',
                [
                    'multiple'                => false,
                    'api_path'                => null,
                    'item_translation_domain' => false,
                ],
            ],
            [
                false,
                'multiple',
                [
                    'multiple'                => false,
                    'api_path'                => null,
                    'item_translation_domain' => false,
                ],
            ],
            [
                true,
                'multiple',
                [
                    'multiple'                => true,
                    'api_path'                => null,
                    'item_translation_domain' => false,
                ],
            ],
        ];
    }
}