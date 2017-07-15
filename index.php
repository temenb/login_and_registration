<?php

require_once 'db/connect.php';
require_once 'lib/router.php';
require_once 'lib/app.php';

$routerConfig = require 'config/router.php';

$router = new Router($routerConfig);
$router->setDefaultRoute('');
$app = new Application($router);

$app->run();