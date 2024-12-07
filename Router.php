<?php

    class Router {

        private static array $data = [];
        public static ?string $Error = null;

        public static function addPage(string $Page, string|callable $Action, string $Method = 'GET'): void {

            $Method = explode('|', $Method);
            $serverMethod = $_SERVER['REQUEST_METHOD'];
            $Page = strtolower($Page);
            $serverPage = $_SERVER['REQUEST_URI'];

            self::$data[$Page] = (object) [
                'Action' => $Action,
                'Method' => $Method,
                'serverMethod' => $serverMethod,
                'Page' => $Page,
                'serverPage' => $serverPage
            ];

        }

        public static function executeRoute(): mixed {

            foreach (self::$data as $value) {

                if (!in_array($value->serverMethod, $value->Method)) {

                    return self::$Error = 'Method not allowed';

                }

                if (!isset(self::$data[$value->serverPage])) {

                    return self::notFound();

                }

                if ($value->Page === $value->serverPage) {

                    if (is_callable($value->Action)) {

                        return call_user_func($value->Action);

                    } else {

                        return self::getController($value->Action);

                    }

                }

            }

            return false;

        }

        private static function getController(string $Action): mixed {

            if (count(explode('@', $Action)) !== 2) {

                return self::$Error = 'Need to be a class and function - class@function';

            }

            list($class, $function) = explode('@', $Action);

            $classUrl = __DIR__ . '/Controllers/' . $class . '.php';

            if (!file_exists($classUrl)) {

                return self::$Error = 'Controller file not found';

            }

            require_once $classUrl;

            if (!class_exists($class)) {

                return self::$Error = 'Class not found';

            }

            if (!method_exists($class, $function)) {

                return self::$Error = 'Function not found';

            }

            return call_user_func([new $class, $function]);


        }

        public static function notFound(): string {

            http_response_code(404);
            return self::$Error = '404 Page Not Found';

        }

    }