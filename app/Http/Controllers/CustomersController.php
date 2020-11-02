<?php

namespace Phpfw\App\Http\Controllers;

use Psr\Http\Message\RequestInterface;
use Phpfw\Component\Support\Facades\View;
use Billing\Domain\Entity\Customer;
use Billing\Domain\Value\Id;
use Billing\Domain\Value\Name;
use Billing\Domain\Value\Email;
use Billing\Domain\Service\CustomerService;
use Billing\Domain\Repository\CustomerRepositoryInterface;

class CustomersController extends Controller
{
	public function __construct(CustomerRepositoryInterface $customer, CustomerService $customerService)
	{
		$this->customerRepository = $customer;
		$this->customerService = $customerService;
	}

	public function index()
	{
		$customers = $this->customerRepository->getAll();

		View::make('customer/index')->with('customers', $customers);
	}

	public function form(RequestInterface $request, $id = '')
	{
		$customer = $this->customerRepository->getById($id);

		if (empty($customer)) {
			$customer = new Customer;
		}

		View::make('customer/form')->with('customer', $customer);
	}

	public function create(RequestInterface $request, $id = '')
	{
		$payloads = (array) $request->getParsedBody();

		if ($request->getRequestMethod() === 'post') {
			$this->customerService->create(
				new Id($id),
				new Name($payloads['name']),
				new Email($payloads['email'])
			);
		}

		$back = $request->getServerParams()['HTTP_REFERER'];

		return header("Location: " . $back);
	}

	public function update(RequestInterface $request, $id = '')
	{
		$payloads = (array) $request->getParsedBody();

		if ($request->getRequestMethod() === 'post') {
			$this->customerService->update(
				new Id($id),
				new Name($payloads['name']),
				new Email($payloads['email'])
			);
		}

		$back = $request->getServerParams()['HTTP_REFERER'];

		return header("Location: " . $back);
	}
}
