<?php

class Router {

    // Store routing data in a static array
    private static array $data = [];
    
    // Variable to store any errors encountered
    public static ?string $Error = null;

    // Method to add a page to the routing list
    public static function addPage(string $Page, string|callable $Action, string $Method = 'GET'): void {

        // Split the method string into an array of methods
        $Method = explode('|', $Method);
        
        // Get the HTTP request method from the server
        $serverMethod = $_SERVER['REQUEST_METHOD'];
        
        // Convert the page name to lowercase
        $Page = strtolower($Page);
        
        // Get the request URI from the server
        $serverPage = $_SERVER['REQUEST_URI'];

        // Store the routing information in the data array
        self::$data[$Page] = (object) [
            'Action' => $Action,
            'Method' => $Method,
            'serverMethod' => $serverMethod,
            'Page' => $Page,
            'serverPage' => $serverPage
        ];
    }

    // Method to execute the route based on the current request
    public static function executeRoute(): mixed {

        // Loop through the routing data to find a match
        foreach (self::$data as $value) {

            // Check if the server method is allowed
            if (!in_array($value->serverMethod, $value->Method)) {

                // Return an error if the method is not allowed
                return self::$Error = 'Method not allowed';
            }

            // Check if the requested page exists in the routing data
            if (!isset(self::$data[$value->serverPage])) {

                // Return a 404 not found error if the page does not exist
                return self::notFound();
            }

            // Check if the current page matches the requested page
            if ($value->Page === $value->serverPage) {

                // If the action is callable, call it
                if (is_callable($value->Action)) {

                    return call_user_func($value->Action);
                } else {

                    // Otherwise, get and execute the controller
                    return self::getController($value->Action);
                }
            }
        }

        // Return false if no route was executed
        return false;
    }

    // Private method to execute the controller's action method
    private static function getController(string $Action): mixed {

        // Ensure the action is in the format 'class@function'
        if (count(explode('@', $Action)) !== 2) {

            return self::$Error = 'Need to be a class and function - class@function';
        }

        list($class, $function) = explode('@', $Action);

        // Determine the path to the controller file
        $classUrl = __DIR__ . '/Controllers/' . $class . '.php';

        // Check if the controller file exists
        if (!file_exists($classUrl)) {

            return self::$Error = 'Controller file not found';
        }

        // Include the controller file
        require_once $classUrl;

        // Check if the class exists
        if (!class_exists($class)) {

            return self::$Error = 'Class not found';
        }

        // Check if the method exists within the class
        if (!method_exists($class, $function)) {

            return self::$Error = 'Function not found';
        }

        // Call the function in the class
        return call_user_func([new $class, $function]);
    }

    // Method to return a 404 error
    public static function notFound(): string {

        // Set the HTTP response code to 404
        http_response_code(404);
        
        // Return the 404 error message
        return self::$Error = '404 Page Not Found';
    }
}
