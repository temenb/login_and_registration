<?php

class App {

    /**
     * @var App
     */
    private static $_instance;

    private $_router;

    private function __construct() {}

    public function setRouter(Router $router) {
        $this->_router = $router;
    }

    private function __clone() {}
    private function __wakeup() {}

    /**
     * @return App
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function run()
    {
        $actionName = $this->_router->getActionName();
        $action = new $actionName();

        if ($action instanceof Action_IAction) {
            session_start();
            $action->run();
            echo $action->getHtml();
        }

    }

    /**
     * @return Router
     */
    public function getRouter() {
        return $this->_router;
    }
}