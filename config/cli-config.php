<?php
require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$config = Setup::createAnnotationMetadataConfiguration(array('module/Application/src/Entity'), true, null, null, false);
$conn = require_once 'config/autoload/local.php';
$entityManager = EntityManager::create($conn['doctrine']['connection']['orm_default']['params'], $config);

return ConsoleRunner::createHelperSet($entityManager);
