<?php
namespace Billing\Domain\Value;

class Description
{
	public function __construct(string $description)
	{
		$this->description = $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
}