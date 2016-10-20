<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// If tests are running directly for the AutocompleteWidgetBundle we can check if there is the vendor/autoload.php file
// in the bundle root directory.
if (file_exists(__DIR__.'/../../../../vendor/autoload.php')) {
    $loader = require_once __DIR__.'/../../../../vendor/autoload.php';

// If the WebServer class is used by another bundle we assume that this package is installed by composer.
} else {
    $loader = require_once __DIR__.'/../../../../../../autoload.php';
}

Debug::enable();

$kernel = new \Mdobak\AutocompleteWidgetBundle\Tests\Functional\Fixtures\AppKernel('test', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);