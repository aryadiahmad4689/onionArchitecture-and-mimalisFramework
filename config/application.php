<?php
return [
	'timezone'	=> 'Asia/Jakarta', 
	'aliases'	=>	[
		'View'		=> \Phpfw\Component\View\View::class,
		'Psudo'		=> \Phpfw\App\Providers\PsudoClass::class, 
	],
	'providers'  =>	[
		\Phpfw\App\Providers\AppServiceProvider::class, 
		\Phpfw\App\Providers\PsudoServiceProvider::class, 
		\Phpfw\App\Providers\LazyPsudoServiceProvider::class, 
		\Billing\App\DoctrineGateway\DoctrineGatewayServiceProvider::class, 
		\Phpfw\App\Providers\BillingServiceProvider::class, 
	],
	'manifest' => ROOT_PATH . '/app/Cache/Services.php', 
];