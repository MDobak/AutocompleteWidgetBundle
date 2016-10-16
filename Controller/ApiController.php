<?php

namespace Mdobak\AutocompleteWidgetBundle\Controller;

use Mdobak\AutocompleteWidgetBundle\DataProvider\DataProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        if (! $request->attributes->has('autocomplete_data_provider')) {
            throw $this->createNotFoundException('Attribute autocomplete_data_provider must be defined for this route!');
        }

        $dataProviderServiceId = $request->attributes->get('autocomplete_data_provider');

        if (! $this->container->has($dataProviderServiceId)) {
            throw $this->createNotFoundException(sprintf('Data provider %s does not exists.', $dataProviderServiceId));
        }

        /** @var DataProviderInterface $dataProvider */
        $dataProvider = $this->container->get($dataProviderServiceId);

        $result = [];
        foreach ($dataProvider->findItemsForAutocomplete($request->query->get('query', '')) as $autocompleteItem) {
            $result[] = [
                'key'   => $autocompleteItem->getKey(),
                'label' => $autocompleteItem->getLabel()
            ];
        }

        return JsonResponse::create($result);
    }
}
