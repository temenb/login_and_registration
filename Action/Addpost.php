<?php

class Action_Addpost extends Action_Abstract
{

    public $title = 'Add post';
    public $teaserLength = 250;

    public $viewTemplate = 'View/addpost.phtml';

    public function run()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = (string)isset($_POST['title']) ? trim($_POST['title']) : '';
            $teaser = (string)isset($_POST['teaser']) ? trim($_POST['teaser']) : '';
            $text = (string)isset($_POST['text']) ? trim($_POST['text']) : '';


            $validaton = true;
            if (empty($title)) {
                $this->messages['errors'][] = 'Text shouldn\'t be empty';
                $validaton = false;
            }
            if (empty($text)) {
                $this->messages['errors'][] = 'Text shouldn\'t be empty';
                $validaton = false;
            }

            $dbLink = Db_Connect::getInstance()->getLink();

            if ($validaton) {
                /* создаем подготавливаемый запрос */

                $teaser = substr($text, 0, $this->teaserLength);
                $stmt = mysqli_prepare(
                    $dbLink,
                    "INSERT INTO post (title, text, teaser, user_id) VALUES (?,?,?,?)"
                );
                if ($stmt) {

                    $userId = App::getUser()->getData('id');
                    mysqli_stmt_bind_param($stmt, "sssi", $title, $teaser, $text, $userId);

                    /* запускаем запрос */
                    $success = mysqli_stmt_execute($stmt);
                    /* закрываем запрос */
                    mysqli_stmt_close($stmt);

                    header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '?r=' . $this->getUrl(get_class()));
                }
            }
        }
    }
}