<?php

require_once 'Lib/spl_auto_loader.php';

$routerConfig = require 'Config/router.php';

$router = new Router($routerConfig);
$router->setDefaultRoute('');

$app = new App($router);

$app->run();