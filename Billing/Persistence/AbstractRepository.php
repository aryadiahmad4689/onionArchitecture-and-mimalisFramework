<?php
namespace Billing\Persistence;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManagerInterface;
use Billing\Domain\Entity\AbstractEntity;
use Billing\Domain\Repository\RepositoryInterface;

abstract class AbstractRepository implements RepositoryInterface
{
	protected $entity;
	protected $entityClass;
	
	public function __construct(EntityManagerInterface $entity)
	{
		if(empty($this->entityClass)) {
			throw new \RuntimeException(
				get_class($this)."::$entityClass is not defined"
			);
		}

		$this->entity = $entity;
	}
	
	public function getById($id)
	{
		$result = $this->entity->find($this->entityClass, $id);
		return $result?$result:false;
	}
	
	public function getAll()
	{
		$result = $this->entity->getRepository($this->entityClass)->findAll();

		return $result;
	}
	
	public function persist(AbstractEntity $entity)
	{
		$this->entity->persist($entity);
		return $this;
	}
	
	public function begin()
	{
		$this->entity->beginTransaction();
		return $this;
	}
	
	public function commit()
	{
		$this->entity->flush();
		$this->entity->commit();
		return $this;
	}
	
	public function flush()
	{
		$this->entity->flush();
		return $this;
	}
}