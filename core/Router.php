<?php

namespace Evgeny;

class Router
{
    protected $container;
    protected $defaultRoute;
    protected $routes;

    protected $controller;
    protected $action;

    public function __construct(Container $container, array $config)
    {
        $this->container = $container;

        if (isset($config['default'])) {
            $this->defaultRoute = $config['default'];
        }

        foreach ($config['routes'] as $route => $controller) {
            $this->routes[$route] = $controller;
        }
    }

    public function generate($route, $parameters)
    {
        $request = $this->container->get('request');

        if (!isset($this->routes[$route])) {
            throw new \RuntimeException(sprintf(
                "Route %s not found",
                $route
            ));
        }

        $uri = $request->getUriForPath($route);
        if (is_array($parameters) && count($parameters) > 0) {
            $uri .= '?' . http_build_query($parameters);
        }

        return $uri;
    }

    /**
     * Попытаться найти controller/action и выполнить его
     */
    public function getResponse()
    {
        $this->detectController();

        if (!$this->controller) {
            throw new \RuntimeException("Page not found");
        }

        if (!class_exists($this->controller)) {
            throw new \RuntimeException(sprintf(
                'Class %s is not exists',
                $this->controller
            ));
        }

        $controller = Controller::factory($this->controller, $this->container);
        $actionName = $this->action;
        return $controller->$actionName();
    }

    private function getControllerName($string)
    {
        $pieces = explode(':', $string);
        array_pop($pieces);
        return implode('\\', $pieces) . 'Controller';
    }

    private function getActionName($string)
    {
        $pieces = explode(':', $string);
        return array_pop($pieces) . 'Action';
    }

    private function detectController()
    {
        $pathInfo = $this->container->get('request')->getPathInfo();

        if ($pathInfo == '/' && $this->defaultRoute) {
            $pathInfo = $this->defaultRoute;
        }

        if (isset($this->routes[$pathInfo])) {
            $this->controller = $this->getControllerName($this->routes[$pathInfo]);
            $this->action = $this->getActionName($this->routes[$pathInfo]);
            return;
        }
    }
}
