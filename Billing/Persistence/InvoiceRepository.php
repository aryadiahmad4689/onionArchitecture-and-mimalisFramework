<?php
namespace Billing\Persistence;

use Billing\Domain\Repository\InvoiceRepositoryInterface;

class InvoiceRepository extends AbstractRepository implements InvoiceRepositoryInterface
{
	protected $entityClass = 'Billing\Domain\Entity\Invoice';
}