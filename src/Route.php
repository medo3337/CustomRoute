<?php
/*
 * Basic Route Class
 * @author: Mohamed Abowarda
 *
 */

namespace CustomFramework;

use App\Controllers;

class Route
{
    /**
     * @var array
     */
    private static $routes = [];

    /**
     * To store array of resources
     */
    private static $resources = [];

    /**
     * Register a new GET route.
     *
     * @param string $uri
     * @param string|Closure $action
     * @return void
     */
    public static function get($uri, $action)
    {
        self::registerRoute('GET', $uri, $action);
    }

    /**
     * Register a new POST route.
     *
     * @param string $uri
     * @param string|Closure $action
     * @return void
     */
    public static function post($uri, $action)
    {
        self::registerRoute('POST', $uri, $action);
    }

    /**
     * Register a new PATCH route.
     *
     * @param string $uri
     * @param string|Closure $action
     * @return void
     */
    public static function patch($uri, $action)
    {
        self::registerRoute('PATCH', $uri, $action);
    }

    /**
     * Register a new DELETE route.
     *
     * @param string $uri
     * @param string|Closure $action
     * @return void
     */
    public static function delete($uri, $action)
    {
        self::registerRoute('DELETE', $uri, $action);
    }

    /**
     * A resource will automatically generate controller routes for the functions (index, get, create, update and delete)
     *
     * @param string $resource
     * @param string $controllerName
     * @return void
     */
    public static function resource($name, $controllerName)
    {
        $nameArray = explode('.', $name);
        $uriWithArgs = '';
        for ($i = 0; $i < count($nameArray); $i++) {
            $uriWithArgs .= $nameArray[$i] . '/{arg' . $i . '}';
            if ($i < count($nameArray) - 1) {
                $uriWithArgs .= '/';
            }
        }

        // Register routes
        self::registerRoute('GET', dirname($uriWithArgs), $controllerName . '@index');
        self::registerRoute('GET', $uriWithArgs, $controllerName . '@get');
        self::registerRoute('POST', dirname($uriWithArgs), $controllerName . '@create');
        self::registerRoute('PATCH', $uriWithArgs, $controllerName . '@update');
        self::registerRoute('DELETE', $uriWithArgs, $controllerName . '@delete');
    }

    /**
     * Register a new route
     *
     * @param string $methods
     * @param string $uri
     * @param string|Closure $action
     * @return void
     */
    protected static function registerRoute($methods, $uri, $action)
    {
        if (!preg_match('/[^-:\/_{}()a-zA-Z\d]/', $uri)) {
            $methodArray = explode('|', $methods);
            foreach ($methodArray as $method) {
                if (!isset(self::$routes[$methods])) {
                    self::$routes[$methods] = [];
                }

                // Remove the starting slash (/) if it exists
                $uri = (substr($uri, 0, 1) == '/') ? substr($uri, 1, strlen($uri)) : $uri;

                self::$routes[$method][] = [$uri, $action];
            }
        }
    }

    /**
     * Match HTTP request based on the registered routes
     *
     * @return true for successful route, false if no route was found
     */
    public static function match()
    {
        $directory = dirname($_SERVER['PHP_SELF']);
        $requestUri = substr($_SERVER['REQUEST_URI'], strlen($directory) + 1, strlen($_SERVER['REQUEST_URI']));
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset(self::$routes[$method])) {
            // No match
            return;
        }

        $routes = self::$routes[$method];
        // Lookup the available routes to find a match
        for ($i = 0; $i < count($routes); $i++) {
            $pattern = $routes[$i][0];

            // Replace (/) with /?
            $pattern = preg_replace('#\(/\)#', '/?', $pattern);
            $allowedChars = '[a-zA-Z0-9\_\-]+';

            // Replace {parameter} with (?<parameter>[a-zA-Z0-9\_\-]+) and build final regular expression
            $regEx = '@^' . preg_replace('/{(' . $allowedChars . ')}/', '(?<$1>' . $allowedChars . ')', $pattern) . '$@D';

            // Check if this is a match
            preg_match($regEx, $requestUri, $matches);

            // If this is a match
            if (count($matches) > 0) {
                // Get arguments
                $args = array_intersect_key($matches, array_flip(array_filter(array_keys($matches), 'is_string')));

                if (is_callable($routes[$i][1])) {
                    // This is a closure
                    call_user_func_array($routes[$i][1], $args);
                } else {
                    // Call controller function
                    $routeInfo = explode('@', $routes[$i][1]);
                    $controllerName = 'App\Controllers\\' . $routeInfo[0];
                    $controller = new $controllerName();

                    // Call controller function
                    call_user_func_array([$controller, $routeInfo[1]], $args);
                }
                return true;
            }
        }
        return false;
    }
}