<?php

require_once __DIR__.'/../vendor/autoload.php';

use Phpfw\Component\Bootstrapper\BootstrapFactory;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH',  realpath(__DIR__ . DS . '..'));
define('CONFIG_ROOT_PATH', realpath(__DIR__ . DS . '..' . DS. 'config'));

$bootstrap = new BootstrapFactory;
$app = $bootstrap->bootstrap();

return $app;