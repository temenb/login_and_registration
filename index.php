<?php

require_once 'Lib/spl_auto_loader.php';

$routerConfig = require 'Config/router.php';

$router = new Router($routerConfig);
$router->setDefaultRoute('');

$app = App::getInstance();
$app->setRouter($router);

$app->run();