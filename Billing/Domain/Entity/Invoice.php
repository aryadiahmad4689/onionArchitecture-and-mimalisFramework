<?php
namespace Billing\Domain\Entity;

use Billing\Domain\Value\Total;

/**
 * @Entity
 * @table(name="invoices")
 */
class Invoice extends AbstractEntity
{
	/**
	 * @ManyToOne(targetEntity="Order")
	 * @JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
	 */
	protected $order;

    /**
	 * @Column(name="invoice_date", type="datetime")
	 */
	protected $invoiceDate;

	/**
	 * @Column(type="decimal")
	 * @Column(length=10)
	 */
	protected $total;
	
	public function getOrder()
	{
		return $this->order;
	}

	public function setOrder(Order $order)
	{
		$this->order = $order;
		return $this;
	}

	public function getInvoiceDate()
	{
		return $this->invoiceDate;
	}

	public function setInvoiceDate(\DateTime $invoiceDate)
	{
		$this->invoiceDate = $invoiceDate;
		return $this;
	}

	public function getTotal()
	{
		return $this->total;
	}

	public function setTotal(Total $total)
	{
		$this->total = $total->getTotal();
		return $this;
	}
}