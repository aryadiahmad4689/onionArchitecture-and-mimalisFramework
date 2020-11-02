<?php
namespace Phpfw\Component\Bootstrapper;

use Phpfw\Component\Foundation\Application;

class Bootstrap implements BootstrapInterface
{
	/**
	 * The appliciation instance
	 * @var \Phpfw\Component\Foundation\Application $app
	 */
	protected $app;

	/**
	 * @param \Repository\Component\Foundation\Application $app
	 */	
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * @{inheritdoc}
	 * See \Repository\Component\Bootstrapper\BootstrapInterface::bootstrap()
	 */
	public function bootstrap()
	{
		//
	}
}