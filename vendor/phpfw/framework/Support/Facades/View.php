<?php
namespace Phpfw\Component\Support\Facades;

class View extends Facade
{
	/**
	 * 
	 * Get the registered name of the component.
	 *
	 * @return string
	 *
	 * @throws \RuntimeException
	 * 
	 */
	public static function getFacadeAccesor()
	{
		return 'view';
	}
}