<?php

require_once '../vendor/autoload.php';

use App\core\Router;
use App\controllers\front\MainController;
use App\controllers\back\JobController;
use App\core\Session;
use App\core\Validator;
use App\controllers\front\AuthController;
use App\core\Auth;

$router = Router::getRouter();

$router->get('about', function () {
    return 'about page';
});

$router->get('session', function () {
    $session = Session::getInstance();
    $session->set('name', 'ahmed');
    $session->set('age', 20);

    echo "Name: " . $session->get('name') . '<br>';
    echo "age: " . $session->get('age') . '<br>';

    $session->remove('age');
    echo "age after remove: " . $session->get('age', 'Removed');
});

$router->get('test-validator', function () {
    $formData = [
        'name' => '',
        'email' => 'bad-email',
        'password' => '123'
    ];

    $validator = new Validator($formData);
    $validator->required('name')
        ->required('email')
        ->email('email')
        ->required('password')
        ->min('password', 8);

    if ($validator->fails()) {
        echo "<h2>Validation Failed ❌</h2>";
        echo "<pre>";
        print_r($validator->errors());
        echo "</pre>";

        echo "<h3>Individual Errors:</h3>";
        echo "Name error: " . $validator->getError('name') . "<br>";
        echo "Email error: " . $validator->getError('email') . "<br>";
        echo "Password error: " . $validator->getError('password') . "<br>";
    } else {
        echo "<h2>Validation Passed ✅</h2>";
    }

    echo "<hr>";

    $validData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123'
    ];

    $validator2 = new Validator($validData);
    $validator2->required('name')
        ->required('email')
        ->email('email')
        ->required('password')
        ->min('password', 8);

    if ($validator2->passes()) {
        echo "<h2>Valid Data Passed ✅</h2>";
    }
});

$router->get('register', [AuthController::class, 'showRegister']);
$router->post('register', [AuthController::class, 'register']);
$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);
$router->get('logout', [AuthController::class, 'logout']);

$router->get('dashboard', function () {
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }

    require_once '../app/views/main/dashboard.php';
});

$router->dispatch();
