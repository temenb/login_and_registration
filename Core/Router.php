<?php

namespace Core;

class Router
{
    private $config = array();
    private $reverseConfig = array();
    const REQUEST_PARAM = 'r';

    private $defaultRoute = 'default';

    public function setDefaultRoute($route)
    {
        settype($route, 'string');
        $config = $this->getConfig();
        if (!isset($config[$route])) {
            ///@TODO add logging here.
            throw new \Exception('Default route doesn\'t exist.');
        }
        $this->defaultRoute = $route;
    }

    public function setConfig($config, $defaultRoute)
    {
        $_config = array();
        $this->reverseConfig = array();
        foreach ($config as $controller => $actions) {
            if (!is_array($actions)) {
                ///@TODO add logging here.
                continue;
            }
            foreach ($actions as $path => $action) {
                if (isset($_config[$path])) {
                    ///@TODO add logging here.
                    unset($this->reverseConfig[$_config[$path]['controller']][$_config[$path]['action']]);
                }
                $_config[$path] = array(
                    'controller' => $controller,
                    'action' => $action,
                );
                $this->reverseConfig[$controller][$action] = $path;
            }
        }
        $this->config = $_config;
        $this->setDefaultRoute($defaultRoute);
    }

    public function getControllerAction($route = false)
    {
        if (!$route) {
            $route = $this->getRouteFromRequest($this->getDefaultRoute());
        }

        return isset($this->config[$route]) ? $this->config[$route] : false;
    }

    public function getUrl($controller, $action)
    {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME']
        . '?' . Router::REQUEST_PARAM . '=' . $this->getRequestParhValue($controller, $action);
    }

    public function getRequestParhValue($controller, $action)
    {
        $controller = strtolower($controller);
        $action = strtolower($action);
        return isset($this->reverseConfig[$controller][$action])
            ? $this->reverseConfig[$controller][$action]
            : $this->getDefaultRoute();
        //@TODO add logging here for not existant controller/action.
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getReverseConfig()
    {
        return $this->reverseConfig;
    }

    /**
     * @return string
     */
    public function getRouteFromRequest($default = '')
    {
        return (string) isset($_REQUEST[self::REQUEST_PARAM])
            ? $_REQUEST[self::REQUEST_PARAM]
            : $default;
    }

    /**
     * @return string
     */
    public function getDefaultRoute()
    {
        return $this->defaultRoute;
    }
}
