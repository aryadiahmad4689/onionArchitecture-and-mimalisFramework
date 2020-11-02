<?php
namespace Billing\Domain\Factory;

use Billing\Domain\Entity\Customer;
use Billing\Domain\Value\Name;
use Billing\Domain\Value\Email;

class CustomerFactory
{
	public function createCustomerEntity(Name $name, Email $email)
	{
		$customer = new Customer();
		
		$customer->setName($name);
		$customer->setEmail($email);
		
		return $customer;
	}
}