<?php

namespace Core\Controller;

use Core\App;
use Core\Router;

abstract class AController implements IController
{
    const SRC_DIR = '../src/';
    public $template;
    public $messages = array(
        'errors' => array(),
        'success' => array(),
    );
    public $headerTemplate = 'header.phtml';
    public $footerTemplate = 'footer.phtml';
    public $viewTemplate;

    private $view = array();

    public function setViewTemplate($viewTemplate)
    {
        $this->viewTemplate = (string) $viewTemplate;
    }

    public function getHtml()
    {
        ob_start();
        if (file_exists(self::SRC_DIR . $this->headerTemplate)) {
            require self::SRC_DIR . $this->headerTemplate;
        }
        if (file_exists(self::SRC_DIR . $this->viewTemplate)) {
            require self::SRC_DIR . $this->viewTemplate;
        }
        if (file_exists(self::SRC_DIR . $this->footerTemplate)) {
            require self::SRC_DIR . $this->footerTemplate;
        }
        return ob_get_clean();
    }

    public function __set($name, $value)
    {
        $this->view[$name] = $value;
    }

    public function __get($name)
    {
        return isset($this->view[$name]) ? $this->view[$name] : "";
    }

    public function getUrl($controllerName, $actionName)
    {
        return App::getRouter()->getUrl($controllerName, $actionName);
    }

    public function redirect($controllerName, $actionName)
    {
        header(
            'Location: ' . $this->getUrl($controllerName, $actionName)
        );
    }
}
