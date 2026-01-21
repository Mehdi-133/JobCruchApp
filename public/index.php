<?php
require_once '../vendor/autoload.php';

use App\core\Router;
use App\core\Session;
use App\core\Validator;
use App\controllers\front\AuthController;
use App\controllers\front\JobController;
use App\controllers\back\AuthController as AdminAuthController;
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



$router->dispatch();
