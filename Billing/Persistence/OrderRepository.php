<?php
namespace Billing\Persistence;

use Doctrine\ORM\Query\Expr\Join;
use Billing\Domain\Repository\OrderRepositoryInterface;

class OrderRepository extends AbstractRepository implements OrderRepositoryInterface
{
	protected $entityClass = 'Billing\Domain\Entity\Order';
	
	public function getUninvoicedOrders()
	{
		$builder = $this->entity->createQueryBuilder()
			->select('o')
			->from($this->entityClass, 'o')
			->leftjoin(
				'Billing\Domain\Entity\Invoice', 
				'i', 
				Join::WITH, 
				'i.order = o')
			->where('i.id IS NULL');

		return $builder->getQuery()->getResult();
	}
}