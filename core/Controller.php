<?php

namespace Evgeny;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig_Environment;

abstract class Controller
{
    protected $container;

    public static function factory($class, Container $container)
    {
        $controller = new $class();

        $controller->setContainer($container);

        return $controller;
    }

    /**
     * Сформировать успешный ответ
     *
     * @param string $content
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function success($template, array $parameters = array())
    {
        $content = $this->container->get('twig')
            ->render($template, $parameters);

        return new Response(
            $content,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url    The URL to redirect to
     * @param int    $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    protected function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route      The name of the route
     * @param array  $parameters An array of parameters
     * @param int    $status     The status code to use for the Response
     *
     * @return RedirectResponse
     */
    protected function redirectToRoute($route, array $parameters = array(), $status = 302)
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param mixed  $parameters    An array of parameters
     *
     * @return string The generated URL
     */
    protected function generateUrl($route, $parameters = array())
    {
        return $this->container->get('router')->generate($route, $parameters);
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function getEntityManager()
    {
        return $this->container->get('entity_manager');
    }
}
