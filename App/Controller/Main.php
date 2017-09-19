<?php

namespace App\Controller;

use Core\Controller\AController;

class Main extends AController
{
    public function mainAction()
    {

        if (!(isset($_SESSION['user']['id']) && $_SESSION['user']['id'])) {
            $this->redirect('authorization', 'login');
        }
        $this->setViewTemplate('main.phtml');
        $this->title = 'main';
    }
}
