<?php

define('TESTS_PATH', __DIR__);
define('TESTS_TEMP_DIR', __DIR__.'/temp');
define('VENDOR_PATH', realpath(__DIR__.'/../vendor'));

if (!is_file($autoloadFile = VENDOR_PATH.'/autoload.php')) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install"?');
}

require $autoloadFile;

\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace('Doctrine\\ORM\\Mapping', VENDOR_PATH.'/doctrine/orm/lib');