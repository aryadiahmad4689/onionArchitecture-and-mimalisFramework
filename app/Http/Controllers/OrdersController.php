<?php
namespace Phpfw\App\Http\Controllers;

use Psr\Http\Message\RequestInterface;
use Phpfw\Component\Support\Facades\View;
use Phpfw\Component\Http\Response;
use Billing\Domain\Value\Id;
use Billing\Domain\Value\Total;
use Billing\Domain\Entity\Order;
use Billing\Domain\Value\Description;
use Billing\Domain\Value\OrderNumber;
use Billing\Domain\Service\OrderService;
use Billing\Domain\Repository\OrderRepositoryInterface;
use Billing\Domain\Repository\CustomerRepositoryInterface;

class OrdersController extends Controller
{
	public function __construct(
		OrderRepositoryInterface $order, 
		CustomerRepositoryInterface $customer, 
		OrderService $orderService)
	{
		$this->orderRepository = $order;
		$this->customerRepository = $customer;
		$this->orderService = $orderService;
	}
	
	public function index()
	{
		$orders = $this->orderRepository->getAll();
		return View::make('order/index')->with('orders', $orders);
	}
	
	public function view($id)
	{
		$order = $this->orderRepository->getById($id);
		
		return View::make('order/view')->with('order', $order);
	}
	
	public function form(RequestInterface $request)
	{
		$customers = $this->customerRepository->getAll();
		
		View::make('order/neworder')->with(array('customers'), array($customers));
	}

	public function purchase(RequestInterface $request)
	{
		$errors = array();
		$payloads = (array) $request->getParsedBody();
		$order = new Order;
		
		if ($request->getRequestMethod() === 'post') {
			$order = $this->orderService->purchase(
				new Id($payloads['customer_id']), 
				new OrderNumber($payloads['order_number']), 
				new Total($payloads['total']), 
				new Description($payloads['description']), 
				$errors
			);
		}
		
		$orderPage = $request->getServerParams()['HOST'].'/orders';
		
		if (empty($errors)) {
			//Lakukan validasi
		}
		
		return header("Location: " . $orderPage);
	}
}