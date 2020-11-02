<?php
namespace Billing\Domain\Value;

class OrderNumber
{
	public function __construct($orderNumber)
	{
		$this->orderNumber = $orderNumber;
	}
	
	public function getOrderNumber()
	{
		return $this->orderNumber;
	}
}