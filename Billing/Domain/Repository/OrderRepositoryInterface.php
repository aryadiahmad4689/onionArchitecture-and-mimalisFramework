<?php
namespace Billing\Domain\Repository;

interface OrderRepositoryInterface extends RepositoryInterface
{
	public function getUninvoicedOrders();
}