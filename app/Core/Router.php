<?php

namespace App\Core;

class Router {
    protected $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch($method, $uri) {
        $uri = ($uri !== '/') ? rtrim($uri, '/') : $uri;
        $method = strtoupper($method);

        // 1. Kiểm tra xem đường dẫn (URI) này có tồn tại trong danh sách hệ thống không
        $pathExists = false;
        foreach ($this->routes as $routeMethod => $paths) {
            if (isset($paths[$uri])) {
                $pathExists = true;
                break;
            }
        }

        if ($pathExists) {
            // Nếu đường dẫn tồn tại nhưng sai phương thức (Ví dụ: GET thay vì POST)
            if (!isset($this->routes[$method][$uri])) {
                http_response_code(405);
                $error405 = __DIR__ . '/../../views/errors/405.php';
                if (file_exists($error405)) { include $error405; } else { echo "405 Method Not Allowed"; }
                exit;
            }

            // Lấy handler
            $handler = $this->routes[$method][$uri];
            $controllerClass = $handler[0];
            $action = $handler[1];

            // 🌟 CHỈNH SỬA: Kiểm tra tính toàn vẹn của Controller trước khi khởi tạo
            if (!class_exists($controllerClass) || !method_exists($controllerClass, $action)) {
                http_response_code(404);
                $error404 = __DIR__ . '/../../views/errors/404.php';
                if (file_exists($error404)) { include $error404; } else { echo "404 Not Found"; }
                exit;
            }

            $controller = new $controllerClass();
            return $controller->$action();
        }

        // 2. Nếu đường dẫn hoàn toàn không tồn tại (Lỗi 404)
        http_response_code(404);
        $error404 = __DIR__ . '/../../views/errors/404.php';
        if (file_exists($error404)) { include $error404; } else { echo "404 Not Found"; }
        exit;
    }
}