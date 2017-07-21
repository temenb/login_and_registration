<?php

class Action_Main extends Action_Abstract {

    public $viewTemplate = 'View/main.phtml';

    public function run() {
        if (!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'])) {
            header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '?r=login');
        }
        $dbLink = Db_Connect::getInstance()->getLink();
        if ($stmt = mysqli_prepare($dbLink, "select * from post")) {
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $this->posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

            mysqli_stmt_close($stmt);


        }
    }
}