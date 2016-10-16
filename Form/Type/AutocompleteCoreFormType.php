<?php

namespace Mdobak\AutocompleteWidgetBundle\Form\Type;

use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderCollectionInterface;
use Mdobak\AutocompleteWidgetBundle\Form\Transformer\ItemsToKeysTransformer;
use Mdobak\AutocompleteWidgetBundle\Form\Transformer\ItemToKeyTransformer;
use Mdobak\AutocompleteWidgetBundle\Routing\ApiPathFinder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AutocompleteCoreFormType.
 */
class AutocompleteCoreFormType extends AbstractType
{
    /**
     * @var DataProviderCollectionInterface
     */
    protected $dataProviderCollection;

    /**
     * @var ApiPathFinder
     */
    protected $apiPathFinder;

    /**
     * AutocompleteFormType constructor.
     *
     * @param DataProviderCollectionInterface $dataProviderCollection
     * @param ApiPathFinder                   $apiPathFinder
     */
    public function __construct(DataProviderCollectionInterface $dataProviderCollection, ApiPathFinder $apiPathFinder)
    {
        $this->dataProviderCollection = $dataProviderCollection;
        $this->apiPathFinder          = $apiPathFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['data_provider'])) {
            throw new InvalidConfigurationException('Option "data_provider" must be set.');
        }

        if ($options['multiple']) {
            $this->buildMultipleItemForm($builder, $options);
        } else {
            $this->buildSingleItemForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $dataProvider = $this->dataProviderCollection->get($options['data_provider']);

        if ($options['api_path']) {
            $view->vars['api_path'] = $options['api_path'];
        } else {
            $view->vars['api_path'] = $this->apiPathFinder->findApiPathForDataProvider($options['data_provider']);
        }

        if ($options['multiple']) {
            $view->vars['full_name'] .= '[]';

            $value = [];
            foreach (($form->getData() ?: []) as $item) {
                $value[] = $dataProvider->wrapItem($item);
            }
        } else {
            $value = $dataProvider->wrapItem($form->getData());
        }

        $view->vars['value']                   = $value;
        $view->vars['multiple']                = $options['multiple'];
        $view->vars['item_translation_domain'] = $options['item_translation_domain'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'multiple'                => false,
                'data_provider'           => null,
                'api_path'                => null,
                'item_translation_domain' => false,
                'compound'                => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildSingleItemForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(
            new ItemToKeyTransformer($this->dataProviderCollection->get($options['data_provider']))
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildMultipleItemForm(FormBuilderInterface $builder, array $options)
    {
        $dataProvider = $this->dataProviderCollection->get($options['data_provider']);
        $builder->addModelTransformer(new ItemsToKeysTransformer($dataProvider));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mdobak_autocomplete_core';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mdobak_autocomplete_core';
    }
}
