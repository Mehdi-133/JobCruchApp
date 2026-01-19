<?php
namespace App\core;

class Router
{

    private static array $routes = [];
    private static $router;

    private function __construct() {}

    public static function getRouter(): Router
    {

        if (!isset(self::$router)) {
            self::$router = new Router();
        }

        return self::$router;
    }


    private function register(string $route, string $method, array|callable $action)
    {

        $route = trim($route, '/');

        self::$routes[$method][$route] = $action;
    }

    public function get(string $route, array|callable $action)
    {
        $this->register($route, 'GET', $action);
    }

    public function post(string $route, array|callable $action)
    {
        $this->register($route, "POST", $action);
    }

    public function dispatch()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        
        $basePath = '/job_dating/public/';
        if (strpos($requestUri, $basePath) === 0) {
            $requestedRoute = substr($requestUri, strlen($basePath));
        } else {
            $requestedRoute = trim($requestUri, '/');
        }
        
        $requestedRoute = trim($requestedRoute, '/');

        $routes = self::$routes[$_SERVER['REQUEST_METHOD']] ?? [];

        foreach ($routes as $route => $action) {

            $routeRegex = preg_replace_callback('/{\w+(:([^}]+))?}/', function ($matches) {
                return isset($matches[1]) ? '(' . $matches[2] . ')' : '([a-zA-Z0-9_-]+)';
            }, $route);

            $routeRegex = '@^' . $routeRegex . '$@';


            if (preg_match($routeRegex, $requestedRoute, $matches)) {

                array_shift($matches);
                $routeParamsValues = $matches;


                $routeParamsNames = [];
                if (preg_match_all('/{(\w+)(:[^}]+)?}/', $route, $matches)) {
                    $routeParamsNames = $matches[1];
                }

                $routeParams = array_combine($routeParamsNames, $routeParamsValues);

                return $this->resolveAction($action, $routeParams);
            }
        }
        return $this->abort('404 Page not found');
    }

    private function resolveAction($action, $routeParams = [])
{
   
    if (is_callable($action)) {
        return call_user_func_array($action, array_values($routeParams));
    }

    
    if (is_array($action)) {
        $controllerName = $action[0]; 
        $methodName = $action[1];   

        $controller = new $controllerName();
        return call_user_func_array([$controller, $methodName], array_values($routeParams));
    }
}

    private function abort($message)
    {
        http_response_code(404);
        echo $message;
    }
}