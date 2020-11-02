<?php
namespace Billing\Domain\Entity;

use Billing\Domain\Value\Total;
use Billing\Domain\Value\Description;
use Billing\Domain\Value\OrderNumber;

/**
 * @Entity
 * @table(name="orders")
 */
class Order extends AbstractEntity
{
	/**
	 * @ManyToOne(targetEntity="Customer")
	 * @JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
	 */
	protected $customer;

	/**
	 * @Column(name="order_number", type="string")
	 * @Column(length=100)
	 */
	protected $orderNumber;

	/**
	 * @Column(type="string")
	 * @Column(length=225)
	 */
	protected $description;

	/**
	 * @Column(type="decimal")
	 * @Column(length=100)
	 */
	protected $total;

	public function getCustomer()
	{
		return $this->customer;
	}
	
	public function setCustomer(Customer $customer)
	{
		$this->customer = $customer;
		return $this;
	}

	public function getOrderNumber()
	{
		return $this->orderNumber;
	}
	
	public function setOrderNumber(OrderNumber $orderNumber)
	{
		$this->orderNumber = $orderNumber->getOrderNumber();
		return $this;
	}

	public function getDescription()
	{
		return $this->description;
	}
	
	public function setDescription(Description $description)
	{
		$this->description = $description->getDescription();
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