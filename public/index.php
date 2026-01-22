<?php
require_once '../vendor/autoload.php';

use App\core\Router;
use App\core\Session;
use App\core\Validator;
use App\controllers\front\AuthController;
use App\controllers\front\JobController;
use App\controllers\back\AuthController as AdminAuthController;
use App\controllers\back\CompanyController;

use App\core\Auth;

$router = Router::getRouter();

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

// Student routes
$router->get('admin/students', [\App\controllers\back\StudentController::class, 'index']);
$router->post('admin/students/store', [\App\controllers\back\StudentController::class, 'store']);
$router->post('admin/students/update', [\App\controllers\back\StudentController::class, 'update']);
$router->post('admin/students/delete', [\App\controllers\back\StudentController::class, 'delete']);


$router->dispatch();
