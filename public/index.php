<?php

require_once '../vendor/autoload.php';

use App\core\Router;
use App\core\Session;
use App\core\Validator;
use App\controllers\front\AuthController;
use App\controllers\back\AuthController as AdminAuthController;
use App\core\Auth;

$router = Router::getRouter();

$router->get('register', [AuthController::class, 'showRegister']);
$router->post('register', [AuthController::class, 'register']);
$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);
$router->get('logout', [AuthController::class, 'logout']);

// Admin routes
$router->get('admin/login', [AdminAuthController::class, 'showLogin']);
$router->post('admin/login', [AdminAuthController::class, 'login']);
$router->get('admin/logout', [AdminAuthController::class, 'logout']);

$router->get('dashboard', function () {
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }

    require_once '../app/views/main/dashboard.php';
});

$router->dispatch();
