<?php
class Router {
    private $routes = [];

    public function add($method, $path, $handler, $middleware = []) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch($method, $uri, $db) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                // Apply middleware
                if (!empty($route['middleware'])) {
                    foreach ($route['middleware'] as $middleware) {
                        if (!$this->applyMiddleware($middleware)) {
                            return;
                        }
                    }
                }

                // Extract parameters from URI
                $params = $this->extractParams($route['path'], $uri);
                
                // Call controller
                $this->callController($route['handler'], $params, $db);
                return;
            }
        }

        // Route not found
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Route not found'
        ]);
    }

    private function matchPath($routePath, $uri) {
        // Convert route path to regex pattern
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
    }

    private function extractParams($routePath, $uri) {
        $params = [];
        
        // Extract parameter names from route path
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
        
        // Extract parameter values from URI
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            
            for ($i = 0; $i < count($paramNames[1]); $i++) {
                if (isset($matches[$i])) {
                    $params[$paramNames[1][$i]] = $matches[$i];
                }
            }
        }
        
        return $params;
    }

    private function applyMiddleware($middleware) {
        switch ($middleware) {
            case 'auth':
                return Auth::authenticate();
            case 'admin':
                return Auth::requireRole('admin');
            case 'agent':
                return Auth::requireRole('agent');
            case 'restaurant':
                return Auth::requireRole('restaurant');
            default:
                return true;
        }
    }

    private function callController($handler, $params, $db) {
        list($controllerName, $method) = explode('@', $handler);
        
        $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Controller not found'
            ]);
            return;
        }

        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Controller class not found'
            ]);
            return;
        }

        $controller = new $controllerName($db);
        
        if (!method_exists($controller, $method)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Controller method not found'
            ]);
            return;
        }

        $controller->$method($params);
    }
}
?>
