<?php

class Router {
    private $_config = array();

    private $_defaultRoute;

    public function __construct(array $config) {
        $this->_config = $config;
    }

    public function setDefaultRoute($route) {
        $this->_defaultRoute = (string) $route;
    }

    public function getActionName() {
        $route = (string) isset($_REQUEST['r']) ? $_REQUEST['r'] : $this->_defaultRoute;

        if (!isset($this->_config[$route])) {
            $route = $this->_defaultRoute;
        }

        if (isset($this->_config[$route]['filename'])) {
            require_once $this->_config[$route]['filename'];
        }

        return $this->_config[$route]['class'];
    }
}

/*

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
*/