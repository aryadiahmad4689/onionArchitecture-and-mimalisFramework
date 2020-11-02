<?php
namespace Billing\Domain\Entity;

use Billing\Domain\Value\Name;
use Billing\Domain\Value\Email;

/**
 * @Entity
 * @table(name="customers")
 */
class Customer extends AbstractEntity
{
	/**
	 * @Column(type="string")
	 * @Column(length=100)
	 */
	protected $name;

	/**
	 * @Column(type="string")
	 * @Column(length=100)
	 */
	protected $email;

	public function getName()
	{
		return $this->name;
	}

	public function setName(Name $name)
	{
		$this->name = $name->getName();
		return $this;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail(Email $email)
	{
		$this->email = $email->getEmail();
		return $this;
	}
}