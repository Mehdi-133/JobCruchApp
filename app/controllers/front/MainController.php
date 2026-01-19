<?php

namespace App\controllers\front;

use App\models\User;

use App\core\Controller;

class MainController extends Controller
{

    public function login()
    {

        $this->view('main/login');
    }

    public function User()
    {
        $userModel = new User();
        $users = $userModel->All();

        $this->view('main/User', ['users' => $users]);
    }


    public function activeUsers()
    {
        $userModel = new User();
        $users = $userModel->getActiveUsers();
        $this->view('main/User', ['users' => $users]);
    }


    public function findByEmail()
    {
        $email = $_GET['email'] ?? null;

        if (!$email) {
            echo "Email required";
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);
        $this->view('main/User', ['users' => $user ? [$user] : []]);
    }


    
}
