<?php

namespace Mdobak\AutocompleteWidgetBundle\Tests\Functional\Form\Type;

use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderCollection;
use Mdobak\AutocompleteWidgetBundle\Form\Type\AutocompleteCoreFormType;
use Mdobak\AutocompleteWidgetBundle\Routing\ApiPathFinder;
use Mdobak\AutocompleteWidgetBundle\Tests\Functional\Fixtures\KernelProvider;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AutocompleteCoreFormTypeTest extends TypeTestCase
{
    /**
     * @var KernelProvider
     */
    private $kernel;

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

        // we need to use real validator to properly check isValid method result
        /** @var ValidatorInterface $validator */
        $validator = $this->kernel->get('validator');

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new FormTypeValidatorExtension($validator))
            ->getFormFactory()
        ;
    }

    protected function getExtensions()
    {
        $this->kernel = new KernelProvider();

        $this->dataProviderCollection = $this->kernel->get('mdobak_autocomplete_widget.data_provider_collection');
        $this->apiPathFinder = $this->kernel->get('mdobak_autocomplete_widget.routing.api_path_finder');

        // create a type instance with the mocked dependencies
        $type = new AutocompleteCoreFormType($this->dataProviderCollection, $this->apiPathFinder);

        return array(
            // register the type instances with the PreloadedExtension
            new PreloadedExtension([$type], []),
        );
    }

    public function testSubmitValidItemOnNotMultipleForm()
    {
        $form = $this->factory->create(AutocompleteCoreFormType::class, null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => false
        ]);

        $form->submit(1);

        $this->assertTrue($form->isValid(), 'Form should be valid');
    }

    public function testSubmitInvalidItemOnNotMultipleForm()
    {
        $form = $this->factory->create(AutocompleteCoreFormType::class, null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => false
        ]);

        $form->submit('invalidKey');

        $this->assertFalse($form->isValid(), 'Form should not be valid');
        $this->assertEquals(null, $form->getData());
    }

    public function testSubmitValidItemOnMultipleForm()
    {
        $form = $this->factory->create(AutocompleteCoreFormType::class, null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => true
        ]);

        $form->submit([1, 5, 10]);

        $this->assertTrue($form->isValid(), 'Form should be valid');
    }

    public function testSubmitInvalidItemOnMultipleForm()
    {
        $form = $this->factory->create(AutocompleteCoreFormType::class, null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => true
        ]);

        $form->submit([1, 5, 'invalid']);

        $this->assertFalse($form->isValid(), 'Form should not be valid');
        $this->assertEquals(null, $form->getData());
    }

    public function testAutocompleteApiPathOnSingleItemForm()
    {
        $form = $this->factory->create(AutocompleteCoreFormType::class, null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => false
        ]);

        $form->submit(1);

        /** @var \Twig_Environment $twig */
        $twig     = $this->kernel->get('twig');
        $template = $twig->createTemplate('{{ form(form) }}');
        $html     = $template->render(['form' => $form->createView()]);

        $crawler = new Crawler($html);
        $select  = $crawler->filter('select[data-mdobak-autocomplete-api-path]')->first();

        $this->assertEquals('/test', $select->attr('data-mdobak-autocomplete-api-path'));
    }

    public function testAutocompleteApiPathOnMultipleItemsForm()
    {
        $form = $this->factory->create(AutocompleteCoreFormType::class, null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => true
        ]);

        $form->submit([1, 5, 10]);

        /** @var \Twig_Environment $twig */
        $twig     = $this->kernel->get('twig');
        $template = $twig->createTemplate('{{ form(form) }}');
        $html     = $template->render(['form' => $form->createView()]);

        $crawler = new Crawler($html);
        $select  = $crawler->filter('select[data-mdobak-autocomplete-api-path]')->first();

        $this->assertEquals('/test', $select->attr('data-mdobak-autocomplete-api-path'));
    }

    public function testSingleItemFormRendering()
    {
        $form = $this->factory->create(AutocompleteCoreFormType::class, null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => false
        ]);

        $form->submit(1);

        /** @var \Twig_Environment $twig */
        $twig     = $this->kernel->get('twig');
        $template = $twig->createTemplate('{{ form(form) }}');
        $html     = $template->render(['form' => $form->createView()]);

        $crawler = new Crawler($html);
        $select  = $crawler->filter('select[data-mdobak-autocomplete-api-path]')->first();
        $options = $select->filter('option');

        $this->assertEquals(1, $options->count());
        $this->assertEquals(1, $options->eq(0)->attr('value'));
        $this->assertEquals('0label', $options->eq(0)->text());
    }

    public function testMultipleItemsFormRendering()
    {
        $form = $this->factory->create(AutocompleteCoreFormType::class, null, [
            'data_provider' => 'dummy_data_provider',
            'multiple'      => true
        ]);

        $form->submit([1, 5, 10]);

        /** @var \Twig_Environment $twig */
        $twig     = $this->kernel->get('twig');
        $template = $twig->createTemplate('{{ form(form) }}');
        $html     = $template->render(['form' => $form->createView()]);

        $crawler = new Crawler($html);
        $select  = $crawler->filter('select[data-mdobak-autocomplete-api-path]')->first();
        $options = $select->filter('option');

        $this->assertEquals(3, $options->count());
        $this->assertEquals(1, $options->eq(0)->attr('value'));
        $this->assertEquals('0label', $options->eq(0)->text());
        $this->assertEquals(5, $options->eq(1)->attr('value'));
        $this->assertEquals('0label', $options->eq(1)->text());
        $this->assertEquals(10, $options->eq(2)->attr('value'));
        $this->assertEquals('1label', $options->eq(2)->text());
    }
}