<?php

class App {

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

        if ($action instanceof Action_IAction) {
            $action->run();
            echo $action->getHtml();
        }

    }
}