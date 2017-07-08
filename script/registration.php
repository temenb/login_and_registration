<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = (string) isset($_POST['email']) ? trim($_POST['email']) : '';
    $confirmEmail = (string) isset($_POST['email_confirmation']) ? 
            trim($_POST['email_confirmation']) : '';
    $password = (string) isset($_POST['password']) ? 
            trim($_POST['password']) : '';
    $confirmPassword = (string) isset($_POST['password_confirmation']) ? 
            trim($_POST['password_confirmation']) : '';
    
    $validaton = true;
    if (empty($email)) {
        $view['messages']['errors'][] = 'Emails shouldn\'t be empty';
        $validaton = false;        
    }
    if (empty($password)) {
        $view['messages']['errors'][] = 'Password shouldn\'t be empty';
        $validaton = false;        
    }
    if ($email != $confirmEmail) {
        $view['messages']['errors'][] = 'Emails don\'t match';
        $validaton = false;        
    }
    if ($password != $confirmPassword) {
        $view['messages']['errors'][] = 'Passwords don\'t match';
        $validaton = false;        
    }
    
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
            $view['messages']['errors'][] = 'User with email' . $email . ' is already exists';
            $validaton = false;
        }
    }
    
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
                $view['messages']['success'][] = 'Congrads, you are in!';
            } else {
                $view['messages']['errors'][] = 'smth went wrong with database';
                //$view['messages']['errors'][] = mysqli_error($dbLink) ;//newer show database errors responce to user
            }
            /* закрываем запрос */
            mysqli_stmt_close($stmt);
        }
    }
    $view['email'] = $email;
}