<?php

namespace App\controllers\back;

use App\core\Controller;
use App\models\Student;
use App\core\Security;
use App\core\Validator;
use App\core\Auth;

class StudentController extends Controller
{
    public function index()
    {
        // Check if admin is logged in
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            header('Location: /admin/login');
            exit;
        }

        $student = new Student();
        $students = $student->findAllBy('role', 'student');
        $this->view('back/students/index', [
            'students' => $students,
            'csrf_token' => Security::getToken()
        ]);
    }

    public function store()
    {
        // Check if admin is logged in
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            header('Location: /admin/login');
            exit;
        }

        // Verify CSRF token
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: /admin/students');
            exit;
        }

        // Validate input
        $validator = new Validator($_POST);
        $validator->validate([
            'name' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->errors();
            $_SESSION['old'] = $_POST;
            header('Location: /admin/students');
            exit;
        }

        // Check if email already exists
        $student = new Student();
        $existing = $student->findByEmail($_POST['email']);
        if ($existing) {
            $_SESSION['error'] = 'Email already exists';
            $_SESSION['old'] = $_POST;
            header('Location: /admin/students');
            exit;
        }

        // Create student
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role' => 'student'
        ];

        $student->create($data);
        $_SESSION['success'] = 'Student added successfully';
        header('Location: /admin/students');
        exit;
    }

    public function update()
    {
        // Check if admin is logged in
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            header('Location: /admin/login');
            exit;
        }

        // Verify CSRF token
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: /admin/students');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Student ID is required';
            header('Location: /admin/students');
            exit;
        }

        // Validate input
        $validator = new Validator($_POST);
        $validator->validate([
            'name' => 'required|min:3|max:50',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->errors();
            header('Location: /admin/students');
            exit;
        }

        $student = new Student();
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email']
        ];

        // Update password if provided
        if (!empty($_POST['password'])) {
            if ($_POST['password'] !== $_POST['password_confirmation']) {
                $_SESSION['error'] = 'Passwords do not match';
                header('Location: /admin/students');
                exit;
            }
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $student->update($id, $data);
        $_SESSION['success'] = 'Student updated successfully';
        header('Location: /admin/students');
        exit;
    }

    public function delete()
    {
        // Check if admin is logged in
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            header('Location: /admin/login');
            exit;
        }

        // Verify CSRF token
        if (!Security::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: /admin/students');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Student ID is required';
            header('Location: /admin/students');
            exit;
        }

        $student = new Student();
        $student->delete($id);
        
        $_SESSION['success'] = 'Student deleted successfully';
        header('Location: /admin/students');
        exit;
    }
}