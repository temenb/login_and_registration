<?php

class Action_Post extends Action_Abstract {

    public $viewTemplate = 'View/post.phtml';

    public function run() {
        if (!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'])) {
            header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '?r=login');
        }
        if (!(isset($_REQUEST['id']) && $_REQUEST['id'])) {
            header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME']);
        }
        $dbLink = Db_Connect::getInstance()->getLink();
        if ($stmt = mysqli_prepare($dbLink, "select p.id post_id, p.*, u.* from post p left join user u on u.id=p.user_id where p.id = ?")) {

            mysqli_stmt_bind_param($stmt, "i", $_REQUEST['id']);

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $this->post = mysqli_fetch_array($result, MYSQLI_ASSOC);

            if (!$this->post) {
                header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME']);
            }
            mysqli_stmt_close($stmt);
        }
    }
}