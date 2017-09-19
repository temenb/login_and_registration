<?php

require_once "../Core/bootstrap.php";

use Core\App;
use Core\Router;

$router = new Router();
$router->setConfig(parse_ini_file('../Core/config/router.ini', true), 'default');
App::setRouter($router);
$app = App::getInstance();

$app->run();
