<?php

$dbLink = require 'db/connect.php';

define('DEFAULT_ROUTE', 'login');

$route = (string) isset($_REQUEST['r']) ? $_REQUEST['r'] : DEFAULT_ROUTE;
$scriptFilename = 'script' . DIRECTORY_SEPARATOR . $route . '.php';
$viewFilename = 'view' . DIRECTORY_SEPARATOR . $route . '.phtml';
if (!file_exists($scriptFilename) || !file_exists($viewFilename)) {
    $route = DEFAULT_ROUTE;
}

$view = array();
require $scriptFilename; //find voulnerability in this code
require_once 'view/header.phtml';
require $viewFilename; //find voulnerability in this code
require_once 'view/footer.phtml';