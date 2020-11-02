<?php
namespace Billing\Domain\Value;

class Total
{
	public function __construct(string $total)
	{
		$this->total = $total;
	}
	
	public function getTotal()
	{
		return $this->total;
	}
}