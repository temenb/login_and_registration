<?php

require_once 'action/abstract.php';

class ActionLogin extends ActionAbstract {

    public $viewTemplate = 'view/login.phtml';

    public function run() {
        $this->title = 'login';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = (string) isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = (string) isset($_POST['password']) ? trim($_POST['password']) : '';

            /* создаем подготавливаемый запрос */
            $dbLink = DbConnect::getInstance()->getLink();
            if ($stmt = mysqli_prepare($dbLink, "select * from user where email= ?")) {

                /* связываем параметры с метками */
                mysqli_stmt_bind_param($stmt, "s", $email);

                /* запускаем запрос */
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                /* получаем значения */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);


                /* закрываем запрос */
                mysqli_stmt_close($stmt);

                if ($row) {
                    $row += array('salt' => '', 'password' => '');
//        var_export($row);
                    if (md5($password . $row['salt']) == $row['password']) {
                        $this->messages['success'][] = 'congrads, you are logged in!!';
                    } else {
                        $this->messages['errors'][] = 'authorization failed';
                    }
                } else {
                    $this->messages['errors'][] = 'authorization failed';
                }
            }

            $this->email = $email;
        }
    }
}