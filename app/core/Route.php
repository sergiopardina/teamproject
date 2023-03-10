<?php

namespace app\core;

class Route
{
    const CONTROLLER_NAMESPACE = 'app\controllers\\';

    public static function init(): void
    {
        session_start();
        $requestURI = $_SERVER['REQUEST_URI'];

        $requestUriWithoutSearch = explode('?', $requestURI)[0];
        $requestUriWithoutSearch = rtrim($requestUriWithoutSearch, '/');

        $pathComponents = explode('/', $requestUriWithoutSearch);
        $pathComponents = array_slice($pathComponents, 1);

        if (count($pathComponents) > 2) {
            self::notFound();
        }

        $controllerName = 'index';
        if (!empty($pathComponents[0])) {
            $controllerName = strtolower($pathComponents[0]);
        }

        $actionName = 'index';
        if (!empty($pathComponents[1])) {
            $actionName = strtolower($pathComponents[1]);
        }

        if (self::ifTryingToAccessAdmin($controllerName, $actionName)) {
            $controllerName = 'login';
            $actionName = 'index';
        } else if (self::isAuthorized() && $controllerName === 'login' && $actionName !== 'logout'){
            self::redirect('admin', 'index');
        }

            $controllerClass = self::CONTROLLER_NAMESPACE . ucfirst($controllerName) . 'Controller';
        if (!class_exists($controllerClass)) {
            self::notFound();
        }


        $controller = new $controllerClass();
        if (!method_exists($controller, $actionName)) {
            self::notFound();
        }

        self::callAction($controller, $actionName);
    }

    public static function ifTryingToAccessAdmin($controllerName, $actionName)
    {
        return (
            (
                $controllerName !== 'index'
            )
            && $actionName !== 'login'
            && !self::isAuthorized()
        );
    }

    public static function isAuthorized()
    {
        return !empty($_SESSION['authorized']);
    }

    public static function notFound(): void
    {
        http_response_code(404);
        // TODO 404 page
        exit();
    }

    private static function callAction(indexable $controller, $action): void
    {
        $controller->$action();
    }

    public static function url(string $controller = null, string $action = null): string
    {
        $controller = $controller ?? 'index';
        $action = $action ?? 'index';
        return "/{$controller}/{$action}";
    }

    public static function redirect($controller, $action, $get = null): void
    {
        if ($get) {
            header('Location: ' . url($controller, $action) . '?' . $get);
            exit();
        } else {
            header('Location: ' . url($controller, $action));
            exit();
        }

    }
}