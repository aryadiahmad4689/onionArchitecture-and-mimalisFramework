<?php
namespace Billing\Domain\Value;

class Name
{
	public function __construct(string $name)
	{
		$this->name = $name;
	}
	
	public function getName()
	{
		return $this->name;
	}
}