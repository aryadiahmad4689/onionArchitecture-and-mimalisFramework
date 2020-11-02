<?php
namespace Billing\Domain\Service;

use Billing\Domain\Value\Id;
use Billing\Domain\Value\Total;
use Billing\Domain\Value\Description;
use Billing\Domain\Value\OrderNumber;
use Billing\Domain\Entity\Order;
use Billing\Domain\Factory\OrderFactory;
use Billing\Domain\Repository\OrderRepositoryInterface;
use Billing\Domain\Repository\CustomerRepositoryInterface;

class OrderService
{
	public function __construct(
		OrderRepositoryInterface $orderRepository, 
		CustomerRepositoryInterface $customerRepository, 
		OrderFactory $orderFactory)
	{
		$this->orderRepository = $orderRepository;
		$this->customerRepository = $customerRepository;
		$this->orderFactory = $orderFactory;
	}

	public function purchase(
		Id $id, 
		OrderNumber $orderNumber, 
		Total $total, 
		Description $description, 
		array &$errors = array())
	{
		$customer = $this->customerRepository->getById($id->getId());
		
		if (!$customer) {
			$errors['CUSTOMER_NOT_FOUND'] = 'Customer tidak ditemukan';
			return;
		}
		
		$order = $this->orderFactory->createOrderEntity($customer, $orderNumber, $total, $description);
		
		$this->orderRepository->begin();
		$this->orderRepository->persist($order);
		$this->orderRepository->commit();
	}
}