<?php

namespace Evgeny;

class TwigRouterExtension extends \Twig_Extension
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'route',
                array($this, 'getRouteFunction'),
                array(
                    'is_safe' => array('html'),
                    'needs_environment' => true,
                )
            ),
        );
    }

    public function getRouteFunction(\Twig_Environment $environment, $route, $parameters = array())
    {
        return $this->router->generate($route, $parameters);
    }

    public function getName()
    {
        return 'app_router_extension';
    }
}
