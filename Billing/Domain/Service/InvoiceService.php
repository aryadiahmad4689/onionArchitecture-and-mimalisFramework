<?php
namespace Billing\Domain\Service;

use Billing\Domain\Factory\InvoiceFactory;
use Billing\Domain\Repository\OrderRepositoryInterface;
use Billing\Domain\Repository\InvoiceRepositoryInterface;

class InvoiceService
{
	protected $orderRepository;
	protected $invoiceFactory;

	public function __construct(
		OrderRepositoryInterface $orderRepository, 
		InvoiceRepositoryInterface $invoiceRepository, 
		InvoiceFactory $invoiceFactory)
	{
		$this->orderRepository = $orderRepository;
		$this->invoiceRepository = $invoiceRepository;
		$this->invoiceFactory = $invoiceFactory;
	}

	public function generateInvoices()
	{
		$orders = (array) $this->orderRepository->getUninvoicedOrders();
		
		$invoices = [];
		
		foreach ($orders as $order) {
			$invoice = $this->invoiceFactory->createFromOrder($order);
			$invoices[] = $invoice;
			
			$this->invoiceRepository->begin();
			$this->invoiceRepository->persist($invoice);
			$this->invoiceRepository->commit();
		}
		
		return $invoices;
	}
}