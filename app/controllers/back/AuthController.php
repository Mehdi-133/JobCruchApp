<?php

namespace App\controllers\back;

use App\core\Controller;
use App\core\Security;
use App\core\Validator;
use App\core\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        $token = Security::getToken();
        $this->view('back/auth/login', ['csrf_token' => $token]);
    }

    public function login()
    {
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }

        $validator = new Validator($_POST);
        $validator->required('email')
            ->email('email')
            ->required('password');

        if ($validator->fails()) {
            $token = Security::getToken();
            $this->view('back/auth/login', [
                'errors' => $validator->errors(),
                'csrf_token' => $token,
                'old' => $_POST
            ]);
            return;
        }

        if (Auth::attempt($_POST['email'], $_POST['password'])) {
            $this->redirect('admin/dashboard');
        } else {
            $token = Security::getToken();
            $this->view('back/auth/login', [
                'error' => 'Invalid email or password',
                'csrf_token' => $token,
                'old' => $_POST
            ]);
        }
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('admin/login');
    }
}