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

    protected function restoreAuthorization()
    {
        if (!isset($_SESSION['user']) && isset($_COOKIE['userlogin']) && isset($_COOKIE['keylogin'])) {
            $dbLink = Db_Connect::getInstance()->getLink();
            if ($stmt = mysqli_prepare($dbLink, "select * from user where email= ?")) {

                /* связываем параметры с метками */
                mysqli_stmt_bind_param($stmt, "s", $_COOKIE['userlogin']);

                /* запускаем запрос */
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                /* получаем значения */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);


                /* закрываем запрос */
                mysqli_stmt_close($stmt);

                if ($row) {
                    $row += array('salt' => '', 'password' => '');
                    if (md5($row['password'] . $row['salt']) == $_COOKIE['keylogin']) {
                        $_SESSION['user'] = $row;

                    }
                }
            }
        }
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
            $this->restoreAuthorization();
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