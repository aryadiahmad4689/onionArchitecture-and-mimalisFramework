<?php
namespace Billing\Domain\Service;

use Billing\Domain\Value\Id;
use Billing\Domain\Value\Name;
use Billing\Domain\Value\Email;
use Billing\Domain\Entity\Customer;
use Billing\Domain\Factory\CustomerFactory;
use Billing\Domain\Repository\CustomerRepositoryInterface;

class CustomerService
{
	protected $customerFactory;
	protected $customerRepository;

	public function __construct(
		CustomerRepositoryInterface $customerRepository, 
		CustomerFactory $customerFactory)
	{
		$this->customerRepository = $customerRepository;
		$this->customerFactory = $customerFactory;
	}

	public function create(Id $id, Name $name, Email $email)
	{
		$customer = $this->customerRepository->getById($id->getId());
		
		//please add your request validation
		if (!$customer) {
			$customer = $this->customerFactory->createCustomerEntity($name, $email);
			
			$this->customerRepository->begin();
			$this->customerRepository->persist($customer);
			$this->customerRepository->commit();
		} else {
			$customer = new Customer;
		}
		
		return $customer;
	}

	public function update(Id $id, Name $name, Email $email)
	{
		$customer = $this->customerRepository->getById($id->getId());
		
		//please add your request validation
		if ($customer) {
			$customer->setName($name);
			$customer->setEmail($email);
			$this->customerRepository->persist($customer);
			$this->customerRepository->flush();
		}
		
		return $customer;
	}
}