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
        $this->view('front/auth/register', ['csrf_token' => $token, 'page_type' => 'auth']);
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
                'old' => $_POST,
                'page_type' => 'auth'
            ]);
            return;
        }

        // Handle profile image upload
        $profileImage = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            $fileType = $_FILES['profile_image']['type'];
            $fileSize = $_FILES['profile_image']['size'];
            
            if (!in_array($fileType, $allowedTypes)) {
                $token = Security::getToken();
                $this->view('front/auth/register', [
                    'errors' => ['profile_image' => ['Invalid file type. Only JPG, PNG, and GIF are allowed.']],
                    'csrf_token' => $token,
                    'old' => $_POST,
                    'page_type' => 'auth'
                ]);
                return;
            }
            
            if ($fileSize > $maxSize) {
                $token = Security::getToken();
                $this->view('front/auth/register', [
                    'errors' => ['profile_image' => ['File size must not exceed 2MB.']],
                    'csrf_token' => $token,
                    'old' => $_POST,
                    'page_type' => 'auth'
                ]);
                return;
            }
            
            // Create uploads directory if it doesn't exist
            $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('profile_', true) . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                $profileImage = 'uploads/profiles/' . $filename;
            } else {
                error_log('Profile image upload failed during registration. Destination: ' . $destination);
            }
        }

        $userModel = new User();
        $userData = [
            'name' => Security::sanitize($_POST['name']),
            'email' => Security::sanitize($_POST['email']),
            'password' => Security::hashPassword($_POST['password']),
            'speciality' => Security::sanitize($_POST['speciality']),
            'promo' => Security::sanitize($_POST['promo']),
            'role' => 'student'
        ];
        
        if ($profileImage) {
            $userData['profile_image'] = $profileImage;
        }
        
        $userId = $userModel->create($userData);

        $user = $userModel->findById($userId);
        // Don't auto-login, redirect to login page
        $this->redirect('login');
    }


    public function showLogin()
    {
        $token = Security::getToken();
        $this->view('front/auth/login', ['csrf_token' => $token, 'page_type' => 'auth']);
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
                'old' => $_POST,
                'page_type' => 'auth'
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
                'old' => $_POST,
                'page_type' => 'auth'
            ]);
        }
    }


    public function logout()
    {
        Auth::logout();
        $this->redirect('login');
    }
}
