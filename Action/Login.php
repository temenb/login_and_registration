<?php

class Action_Login extends Action_Abstract {

    public $viewTemplate = 'View/login.phtml';

    public function run() {
        $this->title = 'login';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = (string) isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = (string) isset($_POST['password']) ? trim($_POST['password']) : '';

            switch (App::getUser()->login($email, $password)) {
                case 2:
                case 1:
                    $this->messages['errors'][] = 'authorization failed';
                    break;
                case 0:
                    header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '?r=' . $this->getUrl('Action_Main'));
                    exit;
            }

            $this->email = $email;
        }
    }
}