<?php

class User {

    private $_data = array();

    const COOKIE_LOGIN = 'userlogin';

    const COOKIE_KEY = 'keylogin';

    const SESSION_KEY = 'user';

    public function isAnonimus() {
        return empty($this->_data['id']);
    }

    public function __construct(array $data)
    {
        $this->setData($data);
    }

    public function restoreAuthorization()
    {
        if ($this->isAnonimus() && isset($_COOKIE[self::COOKIE_LOGIN]) && isset($_COOKIE[self::COOKIE_KEY])) {
            $dbLink = Db_Connect::getInstance()->getLink();
            if ($stmt = mysqli_prepare($dbLink, "select * from user where email= ?")) {

                /* связываем параметры с метками */
                mysqli_stmt_bind_param($stmt, "s", $_COOKIE[self::COOKIE_LOGIN]);

                /* запускаем запрос */
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                /* получаем значения */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);


                /* закрываем запрос */
                mysqli_stmt_close($stmt);

                if ($row) {
                    $row += array('salt' => '', 'password' => '');
                    if (md5($row['password'] . $row['salt']) == $_COOKIE[self::COOKIE_KEY]) {
                        $this->setData($row);
                        $_SESSION[self::SESSION_KEY] = $row;
                    }
                }
            }
        }
    }

    private function setData(array $data) {
        $this->_data = $data;
    }

    public function login($email, $password) {
        /* создаем подготавливаемый запрос */
        $dbLink = Db_Connect::getInstance()->getLink();
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
                if (md5($password . $row['salt']) == $row['password']) {
                    $this->setData($row);
                    $_SESSION[self::SESSION_KEY] = $row;
                    if (!empty($_POST['remember_me'])) {
                        setcookie(self::COOKIE_LOGIN, $row['email'], time() + 10*365*24*60*60);
                        setcookie(self::COOKIE_KEY, md5($row['password'].$row['salt']), time() + 10*365*24*60*60);
                    }
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 2;
            }
        }
    }

    public function getData($key = null) {
        if(null === $key) {
            return $this->_data;
        } else {
            return isset($this->_data[$key]) ? $this->_data[$key] : null;
        }
    }
}