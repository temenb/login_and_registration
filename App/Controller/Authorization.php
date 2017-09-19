<?php

namespace App\Controller;

use Core\Controller\AController;
use App\Entity\User;
use Core\App;

class Authorization extends AController
{
    public function loginAction()
    {
        $this->setViewTemplate('login.phtml');
        $this->title = 'Login';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = (string) isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = (string) isset($_POST['password']) ? trim($_POST['password']) : '';

            $entityManager = App::getEntityManager();
            $user = $entityManager->getRepository('App\\Entity\\User')
                ->findOneByEmail($email);

            if ($user) {
                if ($user->verifyPassword($password)) {
                    $_SESSION['user']['id'] = $user->getId();
                    $_SESSION['user']['email'] = $user->getEmail();
                    $this->redirect('main', 'main');
                    return;
                } else {
                    $this->messages['errors'][] = 'authorization failed';
                }
            } else {
                $this->messages['errors'][] = 'authorization failed';
            }

            $this->email = $email;
        }
    }

    public function logoutAction()
    {
        unset($_SESSION['user']);

        $this->redirect('authorization', 'login');
    }

    public function registrationAction()
    {
        $this->setViewTemplate('registration.phtml');
        $this->title = 'Registration';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = (string)isset($_POST['email']) ? trim($_POST['email']) : '';
            $confirmEmail = (string)isset($_POST['email_confirmation']) ?
                trim($_POST['email_confirmation']) : '';
            $password = (string)isset($_POST['password']) ?
                trim($_POST['password']) : '';
            $confirmPassword = (string)isset($_POST['password_confirmation']) ?
                trim($_POST['password_confirmation']) : '';
            if ($email != $confirmEmail) {
                $this->messages['errors'][] = 'Emails don\'t match';
            }
            if ($password != $confirmPassword) {
                $this->messages['errors'][] = 'Passwords don\'t match';
            }

            $entityManager = App::getEntityManager();
            $user = new User;
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $entityManager->persist($user);
            $errors = $user->getErrors();
            if ($errors) {
                $this->messages['errors'] += $errors;
            } else {
                $entityManager->flush();
            }
            $this->email = $email;
        }
    }
}
