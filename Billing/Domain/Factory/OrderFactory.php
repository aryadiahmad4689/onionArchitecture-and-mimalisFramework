<?php
namespace Billing\Domain\Factory;

use Billing\Domain\Entity\Order;
use Billing\Domain\Entity\Customer;
use Billing\Domain\Value\Total;
use Billing\Domain\Value\Description;
use Billing\Domain\Value\OrderNumber;

class OrderFactory
{
	public function createOrderEntity(
		Customer $customer, 
		OrderNumber $orderNumber, 
		Total $total, 
		Description $description)
	{
		$order = new Order;
		
		$order->setCustomer($customer);
		$order->setOrderNumber($orderNumber);
		$order->setDescription($description);
		$order->setTotal($total);
		
		return $order;
	}
}