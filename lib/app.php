<?php

require_once 'action/iaction.php';

class Application {

    private $_router;

    public function __construct(Router $router) {
        $this->setRouter($router);
    }

    public function setRouter(Router $router) {
        $this->_router = $router;
    }

    public function run()
    {
        $actionName = $this->_router->getActionName();
        $action = new $actionName;
        if ($action instanceof IAction) {
            $action->run();
            echo $action->getHtml();
        }

    }
}