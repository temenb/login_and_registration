<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/autoload.php";

use Core\App;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = array("../App/Entity");
$isDevMode = false;


$dbParams = parse_ini_file('config/db.ini');

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
App::setEntityManager(EntityManager::create($dbParams, $config));
