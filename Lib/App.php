<?php

class App {

    /**
     * @var App
     */
    private static $_instance;

    private $_router;

    private static $_user;

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
            self::$_user = new User(isset($_SESSION[User::SESSION_KEY]) ? $_SESSION[User::SESSION_KEY] : array());
            self::$_user->restoreAuthorization();
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

    /**
     * @return User
     */
    public static function getUser() {
        return self::$_user;
    }
}