<?php

require_once 'Lib/spl_auto_loader.php';

$routerConfig = require 'Config/router.php';

$router = new Router($routerConfig);
$router->setDefaultRoute('');

session_start();
$app = new App($router);

$app->run();