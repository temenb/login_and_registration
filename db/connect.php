<?php

class DbConnect {
    /**
     * @var DbConnect
     */
    private static $_instance;
    
    /**
     * @var resource
     */
    private $_link;
    
    private function __construct() {
        $this->_link = mysqli_connect('localhost', 'root', '123123', 'site');

        if (!$this->_link) {
            echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
            exit;
        }
    }
    
    private function __clone() {}
    private function __wakeup() {}
    
    /**
     * @return DbConnect
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    /**
     * @return resource
     */
    public function getLink() {
        return $this->_link;
    }
    
}