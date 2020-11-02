<?php
namespace Billing\Domain\Factory;

use Billing\Domain\Value\Total;
use Billing\Domain\Entity\Invoice;
use Billing\Domain\Entity\Order;

class InvoiceFactory
{
	public function createFromOrder(Order $order)
	{
		$invoice = new Invoice();
		
		$invoice->setOrder($order);
		$invoice->setInvoiceDate(new \DateTime());
		$invoice->setTotal(new Total($order->getTotal()));
		
		return $invoice;
	}
}