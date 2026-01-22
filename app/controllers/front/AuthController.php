<?php

namespace App\controllers\front;

use App\models\User;
use App\core\Security;
use App\core\Controller;
use App\core\Auth;
use App\core\Validator;

class AuthController extends Controller
{

    public function showRegister()
    {
        $token = Security::getToken();
        $this->view('front/auth/register', ['csrf_token' => $token]);
    }

    public function register()
    {

        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token');
        }

        $validator = new Validator($_POST);
        $validator->required('name')
            ->min('name', 3)
            ->required('email')
            ->email('email')
            ->required('password')
            ->min('password', 8)
            ->required('promo')
            ->in('promo', ['2020/2021', '2021/2022', '2022/2023', '2023/2024', '2024/2025']);


        if ($validator->fails()) {
            $token = Security::getToken();
            $this->view('front/auth/register', [
                'errors' => $validator->errors(),
                'csrf_token' => $token,
                'old' => $_POST
            ]);
            return;
        }

        $userModel = new User();
        $userId = $userModel->create([
            'name' => Security::sanitize($_POST['name']),
            'email' => Security::sanitize($_POST['email']),
            'password' => Security::hashPassword($_POST['password']),
            'speciality' => Security::sanitize($_POST['speciality']),
            'promo' => Security::sanitize($_POST['promo']),
            'role' => 'student'

        ]);

        $user = $userModel->findById($userId);
        // Don't auto-login, redirect to login page
        $this->redirect('login');
    }


    public function showLogin()
    {
        $token = Security::getToken();
        $this->view('front/auth/login', ['csrf_token' => $token]);
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
            $this->view('front/auth/login', [
                'errors' => $validator->errors(),
                'csrf_token' => $token,
                'old' => $_POST
            ]);
            return;
        }

        if (Auth::attempt($_POST['email'], $_POST['password'])) {
            $this->redirect('jobs');
        } else {
            $token = Security::getToken();
            $this->view('front/auth/login', [
                'error' => 'Invalid email or password',
                'csrf_token' => $token,
                'old' => $_POST
            ]);
        }
    }


    public function logout()
    {
        Auth::logout();
        $this->redirect('login');
    }
}
