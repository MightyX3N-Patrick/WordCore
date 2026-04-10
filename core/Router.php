<?php

class Router {
    private static array $routes = [];

    public static function add(string $method, string $pattern, callable $handler): void {
        self::$routes[] = compact('method', 'pattern', 'handler');
    }

    public static function get(string $pattern, callable $handler): void {
        self::add('GET', $pattern, $handler);
    }

    public static function post(string $pattern, callable $handler): void {
        self::add('POST', $pattern, $handler);
    }

    public static function dispatch(string $method, string $uri): void {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');

        foreach (self::$routes as $route) {
            if (strtoupper($route['method']) !== strtoupper($method)) continue;

            $pattern = '@^' . preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $route['pattern']) . '$@';
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}
