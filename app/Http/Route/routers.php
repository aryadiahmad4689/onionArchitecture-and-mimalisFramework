<?php

use Phpfw\App\Http\Middlewares\AuthMiddleware;
use Phpfw\App\Supports\Psudo;

/**
 * 
 * Study Case
 * Billing Service
 * -----------------------------------------------------------
 * You'll learn to apply anything that you have learned so far
 * in the real case with building billing service
 * You'll see how the magical power work on our framework
 * that we have built
 * 
 */

//Invoices Route Handler
$this->get(
	'invoices', 
	"\Phpfw\App\Http\Controllers\InvoicesController@index"
);

$this->get(
	'invoices/view/(\d+)', 
	"\Phpfw\App\Http\Controllers\InvoicesController@view"
);

$this->get(
	'invoices/uninvoiced', 
	"\Phpfw\App\Http\Controllers\InvoicesController@uninvoiced"
);

$this->post(
	'invoices/generate', 
	"\Phpfw\App\Http\Controllers\InvoicesController@generate"
);

//Orders Route Handler
$this->get(
	'orders', 
	"\Phpfw\App\Http\Controllers\OrdersController@index"
);

$this->get(
	'orders/view/(\d+)', 
	"\Phpfw\App\Http\Controllers\OrdersController@view"
);

$this->get('orders/purchase', "\Phpfw\App\Http\Controllers\OrdersController@form");
$this->post('orders/purchase', "\Phpfw\App\Http\Controllers\OrdersController@purchase");

//Customers Route Handler
$this->get(
	'customers', 
	"\Phpfw\App\Http\Controllers\CustomersController@index"
);

$this->get(
	'customers/create', 
	"\Phpfw\App\Http\Controllers\CustomersController@form"
);

$this->get(
	'customers/update/(\d+)', 
	"\Phpfw\App\Http\Controllers\CustomersController@form"
);

$this->post( 
	'customers/create', 
	"\Phpfw\App\Http\Controllers\CustomersController@create"
);

$this->post( 
	'customers/update/(\d+)', 
	"\Phpfw\App\Http\Controllers\CustomersController@update"
);
//-------------------------------END------------------------------

/**
 * 
 * Lazy Psudo Service Provider Event
 * ---------------------------------
 */
$this->get(
	'event/(\d+)/(\w+)', 
	"\Phpfw\App\Http\Controllers\DispatcherController@register"
);

/**
 * 
 * Lazy Service Provider Example
 * --------------------------------
 */
$this->get('psudo', function() {
	echo $this->app['psudo.lazy'];
});

/**
 * 
 * Psudo Facade Example
 * ---------------------------
 */
$this->get('/psudo/facade', function() {
	echo Psudo::argumentInstanceOf();
});

/**
 * 
 * Direct Offset Route Parameters/Primitives Paramater to View
 * ------------------------------------------------------------
 */
$this->get(
	'/controller/(\w+)/(\d+)', 
	'\Phpfw\App\Http\Controllers\HomeController@html'
);

/**
 * 
 * Offset Route Parameters/Primitives Paramater to View
 * ----------------------------------------------------
 */
$this->get(
	'/controller/to/html/(\w+)/(\d+)', 
	'\Phpfw\App\Http\Controllers\HomeController@html'
);

/**
 * 
 * Anonymous Offset
 * Route Parameters/Primitives Paramater
 * ----------------------------------------------------
 */
$this->get('/anonymous/(\w+)/(\d+)', function($name, $old) {
	echo "My name is {$name}, and I'm {$old} years old";
});

/**
 *
 * Anonymous Middleware Handler demo's
 * ------------------------------------
 */
$this->middleware('GET', '/', function($request, $next) {
	echo nl2br("Hello, I'm Middleware for GET '/' request\n".
		"You Need Passed Me Before You Get What You Want\n\n");
	return $next($request);
});

/**
 *
 * Regular Route Handler
 * ------------------------------------
 */
$this->get('/', function() {
	echo "Hello, I'm the thing what you are looking for";
});

/**
 *
 * Class Middleware Handler Example
 * --------------------------------- */
$this->middleware('GET', '/auth', new AuthMiddleware);
$this->get('/auth', function() {
	echo "Good, You have authenticated!";
});

/**
 * 
 * 404 Handler
 * ---------------------------
 */
$this->set404(function() {
	echo "page not found";
});

