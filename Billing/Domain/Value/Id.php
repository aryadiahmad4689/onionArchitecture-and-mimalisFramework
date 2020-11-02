<?php
namespace Billing\Domain\Value;

class Id
{
	public function __construct(string $id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}
}