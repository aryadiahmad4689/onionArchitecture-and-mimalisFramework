<?php
namespace Phpfw\Component\View;

use Exception;
use Phpfw\Component\Config\Config;

class View
{
	private $make = array();
	
	private $extension = ".php";
	
	public function make($args)
	{
		$this->make = $args;
		return $this;
	}

	public function to($path)
	{
		require_once $this->getTarget($path);
	}

	public function with($variable, $transfers = array())
	{
		if (is_array($variable)) {
			$stacks = array_map(function($next) use ($variable,  $transfers) {
				if(array_key_exists($next, $transfers)) {
					$nexts[$next] = $transfers[$next];
					return $nexts;
				} else {
					for ($i=0;$i<count($variable);$i++) {
						$nexts[$variable[$i]] = $transfers[$i];
					}
					return $nexts;
				}
			}, $variable);

			foreach($stacks as $key => $next) {
				extract($next);
			}

			unset($key,  $next);
		} else {
			${$variable} = $transfers;
			unset($variable,  $transfers);
		}
		
		require_once $this->getTarget();
		
		return $this;
	}
	
	public function getTarget($target = null)
	{
		if ($target !== null) {
			$this->make = $target;
		}
		
		if (is_array($this->make)) {
			$target = strtolower(implode($this->make));
		} else {
			$target = strtolower($this->make);
		}
		
		$target = trim($target, '/');
		$target = trim(Config::get('view')['basepath'], '/') . DS . $target;
		
		return $target . $this->extension;
	}
}
