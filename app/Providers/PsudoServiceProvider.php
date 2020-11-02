<?php
namespace Phpfw\App\Providers;

use Phpfw\Component\Contract\Container\ContainerInterface;

class PsudoServiceProvider extends AppServiceProvider
{
	public function register()
	{
		$this->app->bind('psudo', function($app) {
			return (new PsudoClass($app))->argumentInstanceOf();
		});
	}
}

class PsudoClass
{
	private $app;

	public function __construct(ContainerInterface $app)
	{
		$this->app = $app;
	}
	
	public function argumentInstanceOf()
	{
		if($this->app instanceof ContainerInterface) {
			return "I'm instance of ".ContainerInterface::class;
		}
		
		return "I'm Not instance of ".ContainerInterface::class;
	}
}