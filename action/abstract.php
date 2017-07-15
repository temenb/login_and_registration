<?php

require_once 'action/iaction.php';

abstract class ActionAbstract implements IAction {

    public $messages = array(
        'errors' => array(),
        'success' => array(),
    );
    public $headerTemplate = 'view/header.phtml';
    public $footerTemplate = 'view/footer.phtml';
    public $viewTemplate;

    private $_view = array();

    public function getHtml()
    {
        ob_start();
        require $this->headerTemplate;
        if (file_exists($this->viewTemplate)) {
            require $this->viewTemplate;
        }
        require $this->footerTemplate;
        return ob_get_clean();
    }

    abstract function run();

    public function __set($name, $value)
    {
        $this->_view[$name] = $value;
    }

    public function __get($name)
    {
        return isset($this->_view[$name]) ? $this->_view[$name] : "";
    }
}

