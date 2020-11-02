<?php
namespace Billing\Domain\Entity;

abstract class AbstractEntity{
/**
* @Id
* @Column(type="integer", unique=true)
* @GeneratedValue
*/
protected $id;

public function getId()
{
return $this->id;
}

}