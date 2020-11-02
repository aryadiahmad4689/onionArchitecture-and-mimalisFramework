<?php

require_once __DIR__ . "/bootstrap/application.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\EntityManagerInterface;

$em = $app[EntityManagerInterface::class];

return new HelperSet([
    'em' => new EntityManagerHelper($em), 
    'db' => new ConnectionHelper($em->getConnection())
]);