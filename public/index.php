<?php
require_once '../vendor/autoload.php';

use App\core\Router;
use App\core\Session;
use App\core\Validator;
use App\controllers\front\AuthController;
use App\controllers\front\JobController;
use App\controllers\back\AuthController as AdminAuthController;
use App\controllers\back\CompanyController;
use App\controllers\back\AnnouncementController;
use App\core\Auth;

$router = Router::getRouter();

// Home route
$router->get('', function() {
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }

    $controller = new JobController();
    $controller->profile();
});

$router->get('register', [AuthController::class, 'showRegister']);
$router->post('register', [AuthController::class, 'register']);
$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);
$router->get('logout', [AuthController::class, 'logout']);

// Jobs route
$router->get('jobs', function() {
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }

    $controller = new JobController();
    $controller->index();
});

// Job details route
$router->get('jobs/{id}', function($id) {
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }

    $controller = new JobController();
    $controller->show($id);
});

// Job application route
$router->post('jobs/apply', [JobController::class, 'apply']);

// My applications route
$router->get('my-applications', function() {
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }

    $controller = new JobController();
    $controller->myApplications();
});

// Profile route
$router->get('profile', function() {
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }

    $controller = new JobController();
    $controller->profile();
});

// Update profile route
$router->post('profile/update', function() {
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }

    $controller = new JobController();
    $controller->updateProfile();
});

// Admin routes
$router->get('admin/login', [AdminAuthController::class, 'showLogin']);
$router->post('admin/login', [AdminAuthController::class, 'login']);
$router->get('admin/logout', [AdminAuthController::class, 'logout']);

// Admin dashboard
$router->get('admin/dashboard', function() {
    if (!Auth::check() || Auth::user()['role'] !== 'admin') {
        header('Location: /admin/login');
        exit;
    }

    $controller = new \App\controllers\back\DashboardController();
    $controller->index();
});

// Company routes
$router->get('admin/companies', [CompanyController::class, 'index']);
$router->get('admin/companies/create', [CompanyController::class, 'create']);
$router->post('admin/companies/store', [CompanyController::class, 'store']);
$router->get('admin/companies/edit/{id}', [CompanyController::class, 'edit']);
$router->post('admin/companies/update/{id}', [CompanyController::class, 'update']);
$router->post('admin/companies/delete/{id}', [CompanyController::class, 'delete']);

// Student routes
$router->get('admin/students', [\App\controllers\back\StudentController::class, 'index']);
$router->post('admin/students/store', [\App\controllers\back\StudentController::class, 'store']);
$router->post('admin/students/update', [\App\controllers\back\StudentController::class, 'update']);
$router->post('admin/students/delete', [\App\controllers\back\StudentController::class, 'delete']);

// Announcement routes
$router->get('admin/announcements', [AnnouncementController::class, 'index']);
$router->post('admin/announcements/store', [AnnouncementController::class, 'store']);
$router->post('admin/announcements/update/{id}', [AnnouncementController::class, 'update']);
$router->post('admin/announcements/delete/{id}', [AnnouncementController::class, 'delete']);


$router->post('admin/announcements/toggle/{id}', [AnnouncementController::class, 'toggleStatus']);

// Application routes
$router->get('admin/applications', [\App\controllers\back\ApplicationController::class, 'index']);

$router->dispatch();
