<?php

class Action_Addpost extends Action_Abstract
{

    public $title = 'registration';

    public $viewTemplate = 'View/registration.phtml';

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
            if (empty($teaser)) {
                $this->messages['errors'][] = 'Text shouldn\'t be empty';
                $validaton = false;
            }
            if (empty($text)) {
                $this->messages['errors'][] = 'Text shouldn\'t be empty';
                $validaton = false;
            }

            $dbLink = DbConnect::getInstance()->getLink();

            if ($validaton) {
                /* создаем подготавливаемый запрос */
                $stmt = mysqli_prepare(
                    $dbLink,
                    "INSERT INTO user (email, password, salt) VALUES (?,?,?)"
                );
                if ($stmt) {

                    $salt = md5(mt_rand());
                    $encryptedPassword = md5($password . $salt);
                    /* связываем параметры с метками */
                    mysqli_stmt_bind_param($stmt, "sss", $email, $encryptedPassword, $salt);

                    /* запускаем запрос */
                    $success = mysqli_stmt_execute($stmt);
                    if ($success) {
                        $this->messages['success'][] = 'Congrads, you are in!';
                    } else {
                        $this->messages['errors'][] = 'smth went wrong with database';
                        //$this->messages['errors'][] = mysqli_error($dbLink) ;//newer show database errors responce to user
                    }
                    /* закрываем запрос */
                    mysqli_stmt_close($stmt);
                }
            }
            $this->email = $email;
        }
    }
}