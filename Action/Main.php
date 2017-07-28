<?php

class Action_Main extends Action_Abstract {

    public $title = 'Main page';
    public $viewTemplate = 'View/main.phtml';

    public function run() {
        if (App::getUser()->isAnonimus()) {
            header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '?r=' . $this->getUrl('Action_Login'));
        }
        $dbLink = Db_Connect::getInstance()->getLink();
        if ($stmt = mysqli_prepare($dbLink, "select p.id post_id, p.*, u.* from post p left join user u on p.user_id=u.id")) {
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $this->posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

            mysqli_stmt_close($stmt);


        }
    }
}