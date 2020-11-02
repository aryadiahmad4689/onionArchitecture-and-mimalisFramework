<?php
namespace Phpfw\App\Http\Controllers;

use Phpfw\Component\Support\Facades\View;
use Billing\Domain\Service\InvoiceService;
use Billing\Domain\Repository\OrderRepositoryInterface;
use Billing\Domain\Repository\InvoiceRepositoryInterface;

class InvoicesController extends Controller
{	
	public function __construct(
		InvoiceRepositoryInterface $invoice, 
		OrderRepositoryInterface $order, 
		InvoiceService $invoiceService)
	{
		$this->invoiceRepository = $invoice;
		$this->orderRepository = $order;
		$this->invoiceService = $invoiceService;
	}
	
	public function index()
	{
		$invoices = $this->invoiceRepository->getAll();
		return View::make('invoices/index')->with('invoices', $invoices);
	}
	
	public function uninvoiced()
	{
		return View::make('invoices/uninvoiced')
			->with('orders', $this
			->orderRepository
			->getUninvoicedOrders()
		);
	}
	
	public function generate()
	{
		$invoices = $this->invoiceService->generateInvoices();
		return View::make('invoices/generated')->with('invoices', $invoices);
	}
	
	public function view($id)
	{
		$invoice = $this->invoiceRepository->getById($id);
		
		return View::make('invoices/view')->with(
			array('invoice', 'order'), 
			array($invoice, $invoice->getOrder())
		);
	}
}