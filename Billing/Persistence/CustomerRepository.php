<?php
namespace Billing\Persistence;

use Billing\Domain\Repository\CustomerRepositoryInterface;

class CustomerRepository extends AbstractRepository implements CustomerRepositoryInterface
{
	protected $entityClass = "Billing\Domain\Entity\Customer";
}