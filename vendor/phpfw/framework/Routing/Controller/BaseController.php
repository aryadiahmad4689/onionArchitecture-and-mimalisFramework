<?php
namespace Phpfw\Component\Routing\Controller;

use Phpfw\Component\Contract\Container\ContainerInterface;

abstract class BaseController
{
	protected $app;

    public function __construct()
    {
    	//
    }
    
    public function registerApplication(ContainerInterface $app)
    {
        $this->app = $app;
    }
}