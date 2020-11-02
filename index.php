<?php

// This file allows us to emulate Apache's "mod_rewrite" functionality from the built-in PHP web server.
// Provides a convenient way to test the application without having installed a "real" web server software.

// Usage:
// php -S localhost:8080 index.php

require_once __DIR__.'/bootstrap/application.php';

use Phpfw\Component\Http\ServerRequest;
use Psr\Http\Message\RequestInterface;

$request = new ServerRequest;
$servers = $request->getServerParams();
$uri = parse_url($servers['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);

$allowed = false;
$fileinfo = pathinfo($uri);
$allowedExtensions = array('css', 'js');

if (array_key_exists('extension', $fileinfo)) {
  $allowed = in_array($fileinfo['extension'], $allowedExtensions);
}


if (($uri !== '/') && file_exists(__DIR__ . DS . $uri) && $allowed) {
	return false;
}

$app->make(\Phpfw\App\Http\Kernel::class)->handle($app[RequestInterface::class]);