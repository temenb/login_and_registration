<?php

namespace Core;

use Core\Controller\IController;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class App
{
    const ACTION_POSTFIX = 'Action';
    const CONTROLLER_PREFIX = 'App\\Controller\\';

    /**
     * @var App
     */
    private static $instance;

    private $silent;

    private static $entityManager;

    /**
     * @var Router
     */
    private static $router;

    private function __construct()
    {
    }

    /**
     * @return EntityManager
     */
    public static function getEntityManager()
    {
        return self::$entityManager;
    }

    public static function setEntityManager(EntityManager $entityManager)
    {
        self::$entityManager = $entityManager;
    }

    public static function setRouter(Router $router)
    {
        self::$router = $router;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * @return App
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function run()
    {
        $controllerAction = self::$router->getControllerAction();

        $controller = $this->createController($controllerAction);
        $action = $controllerAction['action'] . self::ACTION_POSTFIX;

        if (!$controller) {
            header('HTTP/1.0 404 Not Found');
            return;
        }

        session_start();
        $controller->$action();
        $responce = $controller->getHtml();
        if ($this->getSilent()) {
            return $responce;
        } else {
            echo $responce;
        }
    }

    /**
     * @return Router
     */
    public static function getRouter()
    {
        return self::$router;
    }

    public function setSilent($silent)
    {
        $this->silent = (bool) $silent;
    }

    public function getSilent()
    {
        return $this->silent;
    }

    private function createController($controllerAction)
    {
        if (!isset($controllerAction['controller']) || !isset($controllerAction['action'])) {
            //@TODO add logging here for not existant controller/action.
            return false;
        }
        $controllerName = self::CONTROLLER_PREFIX . ucfirst($controllerAction['controller']);

        if (!class_exists($controllerName)) {
            //@TODO add logging here for not existant controller/action.
            return false;
        }

        $controller = new $controllerName;
        $action = $controllerAction['action'] . self::ACTION_POSTFIX;
        if (!($controller instanceof IController)) {
            //@TODO add logging here.
            return false;
        }
        if (!method_exists($controller, $action)) {
            //@TODO add logging here.
            return false;
        }

        return $controller;
    }
}
