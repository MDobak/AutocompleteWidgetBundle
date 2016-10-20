<?php

namespace Mdobak\AutocompleteWidgetBundle\Tests\Functional\Fixtures\TestBundle\Controller;

use Mdobak\AutocompleteSelect2Bundle\Form\Type\AutocompleteSelect2FormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $formBuilder = $this->createFormBuilder();

        $formDefinition = json_decode($request->query->get('form', '[]'), true);
        $template       = $request->query->get('template', '@Test/default/index.html.twig');

        foreach ($formDefinition as $field) {
            $type = $field['_type'];
            unset($field['_type']);

            $formBuilder->add('widget1', $type, $field);
        }

        $formBuilder->add('submit', SubmitType::class);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        return $this->render($template, [
            'form' => $form->createView(),
            'data' => [
                'valid'     => $form->isValid(),
                'submitted' => $form->isSubmitted(),
                'data'      => $form->getData()
            ]
        ]);
    }
}
