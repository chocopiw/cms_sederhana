<?php
namespace Core;

class Router
{
    private $routes = [];
    private $params = [];

    public function get($route, $controller)
    {
        $this->addRoute('GET', $route, $controller);
    }

    public function post($route, $controller)
    {
        $this->addRoute('POST', $route, $controller);
    }

    private function addRoute($method, $route, $controller)
    {
        // Convert route parameters to regex pattern
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);
        $route = '/^' . str_replace('/', '\/', $route) . '$/i';
        
        $this->routes[$method][$route] = $controller;
    }

    public function match($url, $method)
    {
        foreach ($this->routes[$method] ?? [] as $route => $controller) {
            if (preg_match($route, $url, $matches)) {
                // Remove numeric keys
                foreach ($matches as $key => $match) {
                    if (is_numeric($key)) {
                        unset($matches[$key]);
                    }
                }
                $this->params = $matches;
                return $controller;
            }
        }
        return false;
    }

    public function run()
    {
        $basePath = trim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $url = trim($_SERVER['REQUEST_URI'], '/');
        if ($basePath && strpos($url, $basePath) === 0) {
            $url = substr($url, strlen($basePath));
            $url = ltrim($url, '/');
        }
        echo "<pre>URL: [$url] | BasePath: [$basePath]</pre>";
        $method = $_SERVER['REQUEST_METHOD'];
        
        $controller = $this->match($url, $method);
        
        if ($controller) {
            $this->dispatch($controller);
        } else {
            $this->notFound();
        }
    }

    private function dispatch($controller)
    {
        list($controllerName, $action) = explode('@', $controller);
        $controllerClass = 'Controllers\\' . $controllerName;
        
        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            
            if (method_exists($controllerInstance, $action)) {
                call_user_func_array([$controllerInstance, $action], $this->params);
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }

    private function notFound()
    {
        http_response_code(404);
        echo "404 - Page Not Found";
    }
} 