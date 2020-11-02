<?php
namespace Billing\App\DoctrineGateway;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DoctrineGateway
{
protected $devMode = true;
protected $entityPath;
public function createFromAnnotation(array $dbParams)
{
$config = Setup::createAnnotationMetadataConfiguration($this
->getEntityPath(), $this
->getDevMode()
);
return EntityManager::create($dbParams, $config);
}

public function setEntityPath($path)
{
$this->entityPath = $path;
}

public function getEntityPath()
{
return $this->entityPath;
}

public function setDevMode($mode = false)
{
$this->devMode = $mode;
}

public function getDevMode()
{
return $this->devMode;
}

public function isDevMode()
{
if($this->devMode) {
return true;
}
return false;
}
}